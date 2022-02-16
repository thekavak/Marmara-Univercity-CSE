#include <stdio.h>
#include <semaphore.h>
#include <pthread.h>
#include <dirent.h>
#include <stdlib.h>
#include <string.h>

#define MAXTASKCOUNT 256

/*
	Aydın Duygu - 150118981
	Hamza Kavak - 150118886
	Ubeydullah Günay - 150117063


*/


int totalWord=0;

//struct to be allocated in dynamic array (contains a word and filename its belong to)
typedef struct WordInfo {

    char word[20];
    char fileName[20];

} WordInfo;

//struct to keep multiple args together for using in thread
typedef struct ThreadArg {

    WordInfo *header;

    pthread_mutex_t *taskQueueMutex;
    pthread_mutex_t *memoryMutex;
    int *allTasksAdded;
    size_t *totalSize;
    pthread_mutex_t *mutexes;
    int *numOfThreads;
    int *indexes;
} ThreadArg;

//struct for file allocation between threads
typedef struct Task {

    char fileName[250];
    struct File *file;

} Task;


Task taskQueue[MAXTASKCOUNT];
int taskCount = 0;

//task added by main thread into queue
void addTask(Task task, ThreadArg *arg) {

    pthread_mutex_lock(&(arg->taskQueueMutex));
    taskQueue[taskCount] = task;
    taskCount++;
    pthread_mutex_unlock(&(arg->taskQueueMutex));

}

//returns index of lastly data inserted dynamic array index
int *findHeighestMemoryIndex(int *indexes, int *numOfThreads) {

    int *index = indexes;
    int j;
    for (j = 1; j < *numOfThreads; j++) {

        if ((indexes)[j] > *index) {
            index = &indexes[j];
        }

    }
    return index;

}

int checkIfWordAlreadyExists(WordInfo* header,int index,char* word){

   int i;
   for(i=0;i<index;i++){

       if (strcmp(word,header[i].word)==0){
           return i;
       }

   }

   return -1;
}


//each thread will work with assigned task
void handleTask(Task *task, ThreadArg *threadArg) {
    long id = (long) pthread_self();
    printf("MAIN THREAD: Assigned \"%s\" to worker thread %ld.\n", task->fileName, id);
    printf("THREAD: %ld starts %s\n", id, task->fileName);
    FILE *file = task->file;

    char word[1024];
    /* assumes no word exceeds length of 1023 */
    while (fscanf(file, " %1023s", word) == 1) {

        int j;
        for (j = 0; j < *threadArg->numOfThreads; j++) {

            //each threads lock the avaible mutex by traversing and using trylock
            if (pthread_mutex_trylock(&threadArg->mutexes[j]) == 0) {

                WordInfo *newWordInfo = malloc(sizeof(WordInfo));
                strcpy(newWordInfo->fileName, task->fileName);
                strcpy(newWordInfo->word, word);

                pthread_mutex_lock(threadArg->memoryMutex);

                //check is dynamically allocated memory is full

                int *heighestIndex = findHeighestMemoryIndex(threadArg->indexes, threadArg->numOfThreads);
                if (sizeof(WordInfo) * (*(heighestIndex)) == (*(threadArg->totalSize))) {

                    //double allocated memory
                    WordInfo *newHeader = realloc(threadArg->header, 2 * (*(threadArg->totalSize)));
                    threadArg->header = newHeader;
                    *threadArg->totalSize = 2 * (*threadArg->totalSize);
                    printf("THREAD %ld: Memory size re allocated as %ld bytes\n",pthread_self(),*threadArg->totalSize);

                }

		totalWord++;

                int k = *heighestIndex;

                int alreadyExists = checkIfWordAlreadyExists(threadArg->header, k, word);
                if (alreadyExists == -1){

                    ((threadArg->indexes))[j] = k + 1;
                    pthread_mutex_unlock(threadArg->memoryMutex);

                    memcpy(&((threadArg->header)[k]), newWordInfo, sizeof(WordInfo));
                    printf("THREAD %ld: \"%s\" added into index %d\n", pthread_self(), word, k);

                    free(newWordInfo);
                    pthread_mutex_unlock(&threadArg->mutexes[j]);
                    break;
                }
                else{
                    pthread_mutex_unlock(threadArg->memoryMutex);
                    printf("THREAD %ld: The word \"%s\" has already located at index %d.\n",pthread_self(),word,alreadyExists);
                    free(newWordInfo);
                    pthread_mutex_unlock(&threadArg->mutexes[j]);
                    break;
                }
            }
        }
    }

    printf("THREAD: %ld finished %s\n", id, task->fileName);

}

//function for every thread to handle
void *threadWork(void *arg) {

    while (1) { //run until break

        Task task;
        int taskExist = 0;

        //lock the mutex so other threads can not access to task queue
        pthread_mutex_lock(((ThreadArg *) arg)->taskQueueMutex);

        if (taskCount > 0) { //if there is task waiting on the queue

            taskExist = 1;
            task = taskQueue[0]; //get the first task waiting

            int i;

            //shift all tasks to former index
            for (i = 0; i < taskCount - 1; i++) {
                taskQueue[i] = taskQueue[i + 1];
            }

            taskCount--;

        } else {
            if (*(((ThreadArg *) arg)->allTasksAdded) == 1) { //if main thread had added all tasks then unlock the mutex and exit
                pthread_mutex_unlock(((ThreadArg *) arg)->taskQueueMutex);
                pthread_exit(0);
            }
        }
        pthread_mutex_unlock(((ThreadArg *) arg)->taskQueueMutex);

        if (taskExist == 1) {
            handleTask(&task, (ThreadArg *) arg);
        }

    }


}


int main(int argc, char *argv[]) {

    int NUMBEROFTHREADS;
    DIR *folder;
    struct dirent *entry;

    int numberOfFiles = 0;

    //check # of args
    if (argc < 2) {
        printf("Error: You should enter directory name and number of threads as arguments! ");
    } else if (argc > 5) {
        printf("Error: Too Many Arguments!");
    } else {
        NUMBEROFTHREADS = atoi(argv[4]);
        void *status;
        int rc;             //for thread create, destroy status
        int j;              //for loops
        int allTasksAdded = 0;  //to keep whether main thread assigned all files
        char *file_type;
        size_t totalSize = NUMBEROFTHREADS*sizeof(WordInfo);    //initially allocated memory size for dynamic array
        int dy_Arr_IndexesWillBeWritten[NUMBEROFTHREADS];   //to keep which index of dynamic array will be the next to be written on

        for (j = 0; j < NUMBEROFTHREADS; j++) {
            dy_Arr_IndexesWillBeWritten[j] = 0;
        }


        pthread_t threads[NUMBEROFTHREADS]; //thread ids will be kept

        pthread_mutex_t taskQueueMutex;     //to prevent threads access task queue simultaneously
        pthread_mutex_t memAllocAndIndexDetermMutex;    //to prevent threads double the memory size same time and to prevent thread reaches findHeighestIndex function same time
        pthread_mutex_t mutexes[NUMBEROFTHREADS];

        //initialize mutexes
        pthread_mutex_init(&taskQueueMutex, NULL);
        pthread_mutex_init(&memAllocAndIndexDetermMutex, NULL);
        for (j = 0; j < NUMBEROFTHREADS; j++) {
            pthread_mutex_init(&mutexes[j], NULL);
        }

        //first initialization for dynamic array
        struct WordInfo *dynamicArrayHeader = NULL;
        dynamicArrayHeader = malloc(NUMBEROFTHREADS * sizeof(struct WordInfo));
        printf("MAIN THREAD: Initially allocated %d bytes for dynamic array.\n",totalSize);

        //put all needed args (dynamic array header  and mutexes) in struct to pass as arg
        ThreadArg *threadArg = malloc(sizeof(ThreadArg));
        threadArg->taskQueueMutex = &taskQueueMutex;
        threadArg->memoryMutex = &memAllocAndIndexDetermMutex;
        threadArg->allTasksAdded = &allTasksAdded;
        threadArg->header = dynamicArrayHeader;
        threadArg->indexes = dy_Arr_IndexesWillBeWritten;
        threadArg->totalSize = &totalSize;
        threadArg->mutexes = mutexes;
        threadArg->numOfThreads = &NUMBEROFTHREADS;



        folder = opendir(argv[2]);
        if (folder == NULL) {
            puts("Directory can not be read");
            return 1;
        }

        //create threads each one will run the threadWork function with arg:threadArg struct
        for (j = 0; j < NUMBEROFTHREADS; j++) {
            rc = pthread_create(&threads[j], NULL, &threadWork, (void *) threadArg);

            if (rc) {
                printf("ERROR: Return code from pthread_join() is %d", rc);
                exit(-1);
            }
        }


        //for all documents inside the folder add a task into queue(so that assign to a worker thread)
        while (entry = readdir(folder)) {
            if (!(!strcmp(entry->d_name, ".") || !strcmp(entry->d_name, ".."))) {

                file_type = strrchr(entry->d_name, '.');

                if (file_type != NULL) {


                    if (strcmp(file_type, ".txt") == 0) { // To compare cutted string to check whether it is ".txt"

                        //wait if taskQueue is full
                        while (taskCount == MAXTASKCOUNT);

                        numberOfFiles++;
                        char filePath[1024];

                        sprintf(filePath, "./%s/%s", argv[2], entry->d_name);
                        FILE *file = fopen(filePath, "r");

                        Task *task = malloc(sizeof(Task));
                        strcpy(task->fileName, entry->d_name);
                        task->file = file;
                        addTask(*task, threadArg);

                    }
                }
            }

        }


        pthread_mutex_lock(&taskQueueMutex);
        *(threadArg->allTasksAdded) = 1;
        pthread_mutex_unlock(&taskQueueMutex);

        //wait for threads
        for (j = 0; j < NUMBEROFTHREADS; j++) {

            rc = pthread_join(threads[j], &status);
            if (rc) {
                printf("ERROR: Return code from pthread_join() is %d", rc);
                exit(-1);
            }

        }

        //destroy mutexes
        pthread_mutex_destroy(&taskQueueMutex);
        pthread_mutex_destroy(&memAllocAndIndexDetermMutex);
        for (j = 0; j < NUMBEROFTHREADS; j++) {
            pthread_mutex_destroy(&mutexes[j]);
        }
    }

    printf("\n\n MAIN THREAD: All done(successfully read %d words with %d threads from %d files.. \n\n", totalWord,NUMBEROFTHREADS,numberOfFiles);


    return 0;
}
