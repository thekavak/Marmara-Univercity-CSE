#include <stdio.h>
#include <unistd.h>
#include <errno.h>
#include <stdlib.h>
#include <string.h>
#include<dirent.h>
#include <signal.h>
#include <sys/wait.h>
#include <fcntl.h>


/*

150118981 - Aydın Duygu
150118886 - Hamza Kavak
150117063 - Ubeydullah Günay


*/

#define CREATE_APPENDFLAGS (O_WRONLY | O_APPEND | O_CREAT )
#define CREATE_FLAGS (O_WRONLY | O_TRUNC | O_CREAT )
#define CREATE_INPUTFLAGS (O_RDWR)
#define CREATE_MODE (S_IRUSR | S_IWUSR | S_IRGRP | S_IROTH)

#define MAX_LINE 128 /* 128 chars per line, per command, should be enough. */

long foregroundPID=-1;

//ctrlz handler function
void catchCtrlZ(int signalNbr) {

    if(foregroundPID!=-1){
        write(STDOUT_FILENO,"\nForeground process stopped!",27);
        kill(foregroundPID,23);
        return;

    }
}

void catchCtrlZProcess() {

    struct sigaction action;
    int status;
    action.sa_handler = catchCtrlZ;
    action.sa_flags = 0;
    status = sigemptyset(&action.sa_mask);
    if (status == -1) {
        perror("Failed to initialize signal set");
        exit(1);
    } // End if
    status = sigaction(SIGKILL, &action, NULL);
    if (status == -1) {
        perror("Failed to set signal handler for SIGTSTP");
        exit(1);
    } // End if



} // End catchCtrlZ




// A linked list node for alias
struct Alias {

    struct Alias *next;
    char aliasText[MAX_LINE / 2 + 1];
    char **aliasArgs;
};


void setup(char inputBuffer[], char *args[], int *background) {
    int length, /* # of characters in the command line */
    i,      /* loop index for accessing inputBuffer array */
    start,  /* index where beginning of next command parameter is */
    ct;     /* index of where to place the next parameter into args[] */

    ct = 0;

    /* read what the user enters on the command line */
    length = read(STDIN_FILENO, inputBuffer, MAX_LINE);

    /* 0 is the system predefined file descriptor for stdin (standard input),
       which is the user's screen in this case. inputBuffer by itself is the
       same as &inputBuffer[0], i.e. the starting address of where to store
       the command that is read, and length holds the number of characters
       read in. inputBuffer is not a null terminated C-string. */

    start = -1;
    if (length == 0)
        exit(0);            /* ^d was entered, end of user command stream */

/* the signal interrupted the read system call */
/* if the process is in the read() system call, read returns -1
  However, if this occurs, errno is set to EINTR. We can check this  value
  and disregard the -1 value */
    if ((length < 0) && (errno != EINTR)) {
        perror("error reading the command");
        exit(-1);           /* terminate with error code of -1 */
    }

    for (i = 0; i < length; i++) { /* examine every character in the inputBuffer */

        switch (inputBuffer[i]) {
            case ' ':
            case '\t' :               /* argument separators */
                if (start != -1) {
                    args[ct] = &inputBuffer[start];    /* set up pointer */
                    ct++;
                }
                inputBuffer[i] = '\0'; /* add a null char; make a C string */
                start = -1;
                break;

            case '\n':                 /* should be the final char examined */
                if (start != -1) {
                    args[ct] = &inputBuffer[start];
                    ct++;
                }
                inputBuffer[i] = '\0';
                args[ct] = NULL; /* no more arguments to this command */
                break;

            default :             /* some other character */
                if (start == -1)
                    start = i;
                if (inputBuffer[i] == '&') {
                    *background = 1;
                    inputBuffer[i - 1] = '\0';
                } else if (inputBuffer[i] == 'a') {

                    int a = 5;

                }
        } /* end of switch */
    }    /* end of for */
    args[ct] = NULL; /* just in case the input line was > 80 */


} /* end of setup routine */


//to get number of args entered
int getSize(char *args[]) {

    int i = 0;

    while (args[i] != NULL) {
        i++;
    }

    return i;
}

// This function prints contents of linked list starting from head
void printAliasList(struct Alias *node) {
    while (node != NULL) {
        printf("\n%s \"", node->aliasText);

        int len = getSize(node->aliasArgs);

        int i;
        for (i = 0; i < len; i++) {

            printf("%s ", node->aliasArgs[i]);
        }
        printf("\"\n");

        node = node->next;
    }
}

//copies arguments into another array
void copyArgs(char *args2[], char *args[]) {


    int i;
    for (i = 0; i < getSize(args); i++) {

        char *str = malloc(sizeof(args[i]));
        strcpy(str, args[i]);
        args2[i] = str;

    }
    args2[i] = NULL;

}

/* Given a reference (pointer to pointer) to the head
   of a list and an int, appends a new node at the end  */
void appendAlias(struct Alias **head_ref, char aliasText[MAX_LINE / 2 + 1], char *aliasArgs[MAX_LINE / 2 + 1]) {
    /* 1. allocate node */
    struct Alias *new_node = (struct Alias *) malloc(sizeof(struct Alias));

    struct Alias *last = *head_ref;  /* used in step 5*/

    /* 2. put in the data  */
    strcpy(new_node->aliasText, aliasText);

    new_node->aliasArgs = malloc(sizeof(aliasArgs));
    copyArgs(new_node->aliasArgs, aliasArgs);


    /* 3. This new node is going to be the last node, so make next of
          it as NULL*/
    new_node->next = NULL;

    /* 4. If the Linked List is empty, then make the new node as head */
    if (*head_ref == NULL) {
        *head_ref = new_node;
        return;
    }

    /* 5. Else traverse till the last node */
    while (last->next != NULL)
        last = last->next;

    /* 6. Change the next of last node */
    last->next = new_node;
    return;
}

//used to remove '&' sign if needed
void removeString(char *args[], char *character) {

    int len = getSize(args);
    int i;
    int index = -1;
    for (i = 0; i < len; i++) {
        if (strcmp(args[i], character) == 0) {
            index = i;
        }
    }

    if (index + 1 == len) {
        args[len - 1] = NULL;
    } else {
        for (i = index; i < len; i++) {
            args[i] = args[i + 1];
        }
        args[len - 1] = NULL;
    }
}

//remove alias from linked list - it is called when unalias command entered
void deleteAlias(struct Alias **head_ref, char *aliasText) {
    // Store head node
    struct Alias *temp = *head_ref, *prev;

    // If head node itself holds the key to be deleted
    if (temp != NULL && strcmp(temp->aliasText, aliasText) == 0) {
        *head_ref = temp->next; // Changed head
        free(temp); // free old head
        return;
    }

    // Search for the key to be deleted, keep track of the
    // previous node as we need to change 'prev->next'
    while (temp != NULL && strcmp(temp->aliasText, aliasText) != 0) {
        prev = temp;
        temp = temp->next;
    }

    // If key was not present in linked list
    if (temp == NULL)
        return;

    // Unlink the node from linked list
    prev->next = temp->next;

    free(temp); // Free memory
}

//searches linked list checks whether alias text exist in the list , if exists returns the node else returns null
struct Alias *search(struct Alias *head, char *aliasText) {
    struct Alias *current = head;  // Initialize current
    while (current != NULL) {
        if (strcmp(aliasText, current->aliasText) == 0) {
            return current;
        }
        current = current->next;
    }
    return NULL;
}

//if first arg is 'alias' then appends the entered command in the alias linked list
void aliasProcess(char *args[], struct Alias **headerAlias) {
    int properAlias = 1;
    int len = getSize(args);

    if (len == 1) {
        printf("Invalid command! You have to enter commands in quotes and an alias text after keyword alias!");
    } else if (len == 2 && strcmp(args[1], "-l") == 0) {

        printAliasList(*headerAlias);
        return;

    }

    int i = 1;
    int indexOpenQuotes = 0;
    char aliasText[128];
    char *aliasArgs[MAX_LINE / 2 + 1] = {NULL};

    if (args[1][0] == '\"') {

        args[1] = args[1] + 1;
        indexOpenQuotes = 1;

        while (indexOpenQuotes == 1) {

            int len = strlen(args[i]);
            if (args[i][len - 1] == '\"') {
                args[i][len - 1] = '\0';
                indexOpenQuotes = 0;
            }

            aliasArgs[i - 1] = args[i];
            i++;

        }

        if (args[i] != NULL) {
            strcpy(aliasText, args[i]);
            aliasArgs[i - 1] = NULL;
        } else {
            properAlias = 0;
        }
        int a = 5;


    } else {
        properAlias = 0;
    }

    appendAlias(headerAlias, aliasText, aliasArgs);
}

int main() {


    char inputBuffer[MAX_LINE]; /*buffer to hold command entered */
    int background; /* equals 1 if a command is followed by '&' */
    char *args[MAX_LINE / 2 + 1]; /*command line arguments */
    struct Alias *headerAlias = NULL;


    while (1) {
        signal(SIGTSTP,SIG_IGN);
        background = 0;
        printf("\nmyshell: ");
        fflush(stdout);
        setup(inputBuffer, args, &background);


        if (strcmp(args[0], "alias") == 0) {

            aliasProcess(args, &headerAlias);

            continue;
        } else if (strcmp(args[0], "unalias") == 0) {
            if (getSize(args) > 2) {
                printf("Invalid format!");
                continue;
            } else {
                deleteAlias(&headerAlias, args[1]);
                int a = 5;
            }
        } else if (strcmp(args[0], "exit") == 0) {

            long pid = -1;

                //check is there any background process
                  pid=  waitpid(-1, NULL, WNOHANG);

            if (pid > 0) {
                printf("There are still running background processes first close them!");
            } else {
                int g_pid = getpgid(getpid());
                killpg(g_pid, 9);
            }


        } else {

            int p_id = fork();

            if (p_id == -1) {//Error
                printf("Error occured while forking !");
                break;
            }

            if (p_id == 0) {// ourchil process

            //search the command in the directories in path
                struct dirent *file;
                char *path = getenv("PATH");
                int result;

                path = strtok(strdup(path), ":");

                int len = getSize(args);

                if (len > 2) {

                    char foutput[50];
                    char finput[50];
                    int fileIn;
                    int fileOut;

                    //from here i/o redirection part is handled
                    if (!strcmp(args[len - 2], ">") && !strcmp(args[len - 4], "<")) {

                        //get file names
                        strcpy(finput, args[len - 3]);
                        strcpy(foutput, args[len - 1]);

                        fileIn = open(finput, CREATE_INPUTFLAGS, CREATE_MODE);
                        fileOut = open(foutput, CREATE_FLAGS, CREATE_MODE);

                        args[len - 4] = NULL;

                        if (fileIn == -1) {
                            perror("Failed to open file");
                            return 1;
                        }

                        //redirect input from stdin to file
                        if (dup2(fileIn, STDIN_FILENO) == -1) {
                            perror("Failed to redirect standart output");
                            return 1;
                        }


                        if (close(fileIn) == -1) {
                            perror("Failed to close the file");
                            return 1;
                        }

                        if (fileOut == -1) {
                            perror("Failed to open file");
                            return 1;
                        }

                        //redirect stdout to file
                        if (dup2(fileOut, STDOUT_FILENO) == -1) {
                            perror("Failed to redirect standart output");
                            return 1;
                        }

                        if (close(fileOut) == -1) {
                            perror("Failed to close the file");
                            return 1;
                        }

                    } else if (!strcmp(args[len - 2], ">>")) {

                        strcpy(foutput, args[len - 1]);
                        fileOut = open(foutput, CREATE_APPENDFLAGS, CREATE_MODE);
                        args[len - 2] = NULL;

                        if (fileOut == -1) {
                            perror("Failed to open file");
                            return 1;
                        }
                        if (dup2(fileOut, STDOUT_FILENO) == -1) {
                            perror("Failed to redirect standart output");
                            return 1;
                        }
                        if (close(fileOut) == -1) {
                            perror("Failed to close the file");
                            return 1;
                        }


                    } else if (!strcmp(args[len - 2], "<")) {

                        strcpy(finput, args[len - 1]);
                        fileIn = open(finput, CREATE_INPUTFLAGS, CREATE_MODE);
                        args[len - 2] = NULL;

                        if (fileIn == -1) {
                            perror("Failed to open file");
                            return 1;
                        }
                        if (dup2(fileIn, STDIN_FILENO) == -1) {
                            perror("Failed to redirect standart output");
                            return 1;
                        }
                        if (close(fileIn) == -1) {
                            perror("Failed to close the file");
                            return 1;
                        }

                    } else if (!strcmp(args[len - 2], ">")) {


                        strcpy(foutput, args[len - 1]);
                        fileOut = open(foutput, CREATE_FLAGS, CREATE_MODE);
                        args[len - 2] = NULL;

                        if (fileOut == -1) {
                            perror("Failed to open file");
                            return 1;
                        }
                        if (dup2(fileOut, STDOUT_FILENO) == -1) {
                            perror("Failed to redirect standart output");
                            return 1;
                        }
                        if (close(fileOut) == -1) {
                            perror("Failed to close the file");
                            return 1;
                        }

                    }

                }

                char **temp = args;


                struct Alias *alias = search(headerAlias, args[0]);
                if (alias != NULL) {
                    temp = alias->aliasArgs;
                    int len2 = getSize(alias->aliasArgs);

                    int i;
                    for (i = 0; i < len2; i++) {

                        if (strcmp(alias->aliasArgs[i], "&") == 0) {
                            background = 1;
                            break;
                        }
                    }
                }


                while (path != NULL) {

                    DIR *dir;
                    dir = opendir(path);

                    if (dir) {

                        while ((file = readdir(dir)) != NULL) {
                            if (strcmp(file->d_name, temp[0]) == 0) {

                                strcat(path, "/");
                                strcat(path, temp[0]);

                                if (background) {

                                    removeString(temp, "&");

                                }

                                result = execv(path, temp);
                                fprintf(stdout, "execv() returned %d\n", result);
                                fprintf(stdout, "errno: %s (%d).\n", strerror(errno), errno);
                            }

                        }
                    }

                    path = strtok(NULL, ":");
                }
                fprintf(stdout, "command not found \n");


            } else {//parent process

                if (background != 1) {


                    signal(SIGTSTP,catchCtrlZ);
                    waitpid(p_id, NULL, 0);
                    foregroundPID=-1;
                } else {
                    waitpid(p_id, NULL, WNOHANG);
                }
            }
        }
    }
}
