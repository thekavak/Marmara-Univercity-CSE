#include <stdio.h>
#include <locale.h> 
#include <string.h>
#include <dirent.h>  
#include <stdlib.h>
#include <math.h>


// Start of part A Master Linked List
struct n{
	char *data;
	char *dataDir;
	char *dataCat;
	struct n *next; 
	struct n *firstO;
	struct n *secondO;
};
typedef struct n node;
	node * root;
// End of part A Master Linked List	

	char categoryArray[3][10] ={"econ","health","magazin"}; // categorys
	int categoryTotalFiles[3] ={0,0,0}; // categorys total files for part C
	char dirPath[20] = "smallDataset/"; // files' path
	char tempDirName[20]; // for the same file control
	
// Start of part B Linked List	
struct node{  
    char *data;
	char *dataCat;
	char *dataUniq;
	int dUCount; // dataUniqCount
	int  count;
    struct node *next;  
};  
typedef struct node nodePtr;
	nodePtr *head, *tail = NULL;  
int a = 0; 
// End of part B Linked List


// Start of SequentialInsert for part A
node * sequentialInsert(node *r , char *value, char dirName[20],char dirCat[20]){ // insert function for part A

	//At the same time. We find first cooccurence
		if(strcmp(tempDirName, dirName)==0){//to do first co-occurence 
		node * check = r;
		while(check != NULL ){ 
		char *ret;
		ret = strstr(check->dataDir, dirName);
		if (strcmp(check->dataDir,dirName)== 0 || ret != NULL ){
	
			if(check->firstO == NULL){
				node * temp = (node *)malloc(sizeof(node));
				temp->data = strdup(value);
				temp->dataDir = strdup(dirName);
				temp->dataCat = strdup(dirCat);
				temp->firstO = NULL;
				temp->secondO = NULL;
				temp->next = NULL;
				check->firstO = temp;	
			}
			else if(check->firstO != NULL){
			
				node * firstOccurence = check;
				while(firstOccurence->firstO != NULL){
					firstOccurence = firstOccurence->firstO;
				}
				node * temp = (node *)malloc(sizeof(node));
				temp->data = strdup(value);
				temp->dataDir = strdup(dirName);
				temp->dataCat = strdup(dirCat);
				temp->firstO = NULL;
				temp->secondO = NULL;
				temp->next = NULL;
				firstOccurence->firstO = temp;
					
			}
		}
			check = check->next;
		}
			
	}
	
	
	if(r == NULL) // if linked list is empty
	{
		r = (node*)malloc(sizeof(node));
		r->data = strdup(value);
		r->dataDir = strdup(dirName);
		r->dataCat = strdup(dirCat);
		r->firstO = NULL;
		r->secondO = NULL;
		r->next = NULL;
		strcpy(tempDirName,dirName);
		return r;
	}
	
	node * check = r;
	while(check != NULL){ // To check whether or not there is the same value.
		if(strcmp(check->data, value)==0){ 
			strcat(check->dataCat,",");
			strcat(check->dataCat,dirCat);
			strcat(check->dataDir,",");
			strcat(check->dataDir,dirName);
			strcpy(tempDirName,dirName);
			return r;	
		}
		check = check->next;
	}
		
		
	if(strcmp(r->data , value) == 1) {
		node * temp = (node *)malloc(sizeof(node));
		temp->data = strdup(value);
		temp->dataDir = strdup(dirName);
		temp->dataCat = strdup(dirCat);
		temp->firstO = NULL;
		temp->secondO = NULL;
		temp->next = r;
		r = temp;
		strcpy(tempDirName,dirName);
		return temp;
	}
		
	node * iter = r; // To find last items in linked list
	while(iter->next != NULL && ( strcmp(iter->next->data ,value) < 0)){
		iter = iter->next; 
	}
	
	node * temp = (node*)malloc(sizeof(node));
	temp->next = iter->next;
	iter->next = temp;
	temp->data = strdup(value);
	temp->dataDir = strdup(dirName);
	temp->dataCat = strdup(dirCat);
	temp->firstO = NULL;
	temp->secondO = NULL;
	strcpy(tempDirName,dirName);
	return r;  

}
// End of sequentialInsert for Part A

//start of second co-occurence function
void findSecondCoOccurence(node *r){
	node * check = r; // listeye bütün elemanlarý ekledim þimdi yapmak istediðim 1 den fazla kategoriye sahip elemanlarý listelemek
	while(check != NULL){
		
		char * findcharacter = strchr(check->dataDir,','); // datadir içerisinde , iþareti var mý diye bakýyor.
		
		if(findcharacter) { // master linked listim oluþtu bazý kelimeler 2 farklý dosyada da olduðu için onlarýn kategorilerine 2 category yazdým
		// þimdi onlarý bulup ayýracaðým.
			char ch = ',';
			int frequency = 1,k;
			for(k = 0; check->dataDir[k] != '\0'; k++)
			   {
			       if(ch == check->dataDir[k])
			           frequency++;
			   }
  				   
			char * catArray[frequency];// kelime içerisindeki kategorileri array liste atýyorum.
			int i=0;
			char tempDataDir[100];
			strcpy(tempDataDir,check->dataDir);
			char * getcat = strtok(tempDataDir,",");
		
			while(getcat != NULL){
				catArray[i] = getcat;
				i++;
				getcat = strtok(NULL,",");
			} 
			
		
			if(frequency == 2) //frequency = linked list dataDir içerisinde birden fazla kategori yazýldý ise onun sayýsýný veriyor. 
			{
				node * checkA = r;
				while(checkA != NULL){
				
					char *compare;
					compare = strstr(checkA->dataDir, catArray[0]);
					
				
					if(compare !=NULL && strcmp(checkA->data,check->data)!=0  )
					{
						node * checkB =r;
						while(checkB!=NULL)
						{
							int flag = 0;
							char *compareB;
							compareB = strstr(checkB->dataDir, catArray[1]);
							if(compareB !=NULL && strcmp(checkB->data, check->data)!=0 ){
								
									if(checkA->secondO == NULL){
										node * temp = (node *)malloc(sizeof(node));
										temp->data = strdup(checkB->data);
										temp->dataDir = strdup(checkB->dataDir);
										temp->dataCat = strdup(checkB->dataCat);
										temp->firstO = NULL;
										temp->secondO = NULL;
										temp->next = NULL;
										checkA->secondO = temp;
										
									}
									else if(checkA->secondO != NULL){
									
										node * secondOccurence = checkA;
										while(secondOccurence->secondO != NULL){
											if(strcmp(secondOccurence->data,checkB->data) == 0) // eüer o iki kelime daha önce baþka bir þekilde eþleþtiler ise flag =11 olsunn
											{
											 flag = 11;	
											}
											secondOccurence = secondOccurence->secondO;
										}
										if(flag == 0){ // flag 11 ise buraya girmiyor ve ekleme iþlemini yapmýyor.
											node * temp = (node *)malloc(sizeof(node));
											temp->data = strdup(checkB->data);
											temp->dataDir = strdup(checkB->dataDir);
											temp->dataCat = strdup(checkB->dataCat);
											temp->firstO = NULL;
											temp->secondO = NULL;
											temp->next = NULL;
											secondOccurence->secondO = temp;	
										}
										
									}
			
							}
							
							checkB = checkB->next;
						}
						
					}
					
				checkA = checkA->next;	
				}
			}
			else if(frequency > 2){ // bir kelime 2den fazla dosyada varsa. Ex: ilk kelimesi 3 dosyada var. 
				int arrayStart = 0;
				int arrayMidde = 0;
				int arrayEnd = frequency-1;
			
				while(arrayStart < arrayEnd)
				{
					
					node * checkA = r;
					while(checkA != NULL){
						char *compare;
						compare = strstr(checkA->dataDir, catArray[arrayStart]);
								if(compare !=NULL && strcmp(checkA->data,check->data)!=0  )
									{
										node * checkB =r;
										while(checkB!=NULL)
										{
											int flag = 0 ;
											char *compareB;
											compareB = strstr(checkB->dataDir, catArray[arrayMidde+1]);
											if(compareB !=NULL && strcmp(checkB->data, check->data)!=0 ){
												
													if(checkA->secondO == NULL){
														node * temp = (node *)malloc(sizeof(node));
														temp->data = strdup(checkB->data);
														temp->dataDir = strdup(checkB->dataDir);
														temp->dataCat = strdup(checkB->dataCat);
														temp->firstO = NULL;
														temp->secondO = NULL;
														temp->next = NULL;
														checkA->secondO = temp;
														
													}
													else if(checkA->secondO != NULL){
													
														node * secondOccurence = checkA;
														while(secondOccurence->secondO != NULL){
																if(strcmp(secondOccurence->data,checkB->data) == 0) // yine ayný iþlem, Eðer o eþleþme daha önce yapýldý ise flag =11 olsun 
																{
																 flag = 11;	
																}
															secondOccurence = secondOccurence->secondO;
														}
														if(flag == 0){
															node * temp = (node *)malloc(sizeof(node));
															temp->data = strdup(checkB->data);
															temp->dataDir = strdup(checkB->dataDir);
															temp->dataCat = strdup(checkB->dataCat);
															temp->firstO = NULL;
															temp->secondO = NULL;
															temp->next = NULL;
															secondOccurence->secondO = temp;
														}		
													}
												
											}
											
											checkB = checkB->next;
										}
										
									}
						
					checkA = checkA->next;
					}
					
					if(arrayMidde+1 < arrayEnd){
						arrayMidde +=1;
					}
					else{
						arrayStart +=1;
					
					}
					
				}
			}
			
			
				
		
		}
		check= check->next;
	}
}
//end of second co-occurene function

//Start of Function to add new Items for Part B
int addNodeForPartB(char *value,char dirCat[20], char dataUniqq[20]) { 

    	struct node *current = head; 
			while(current != NULL){  //  To check whether or not there is the same value.
				if(strcmp(current->data, value)==0 && (strcmp(current->dataCat,dirCat)==0 )){
					current->count +=1; // if there is the same value, we will increase its count
					if(strcmp(current->dataUniq,dataUniqq)<0){	
						current->dUCount+=1;//I don't know what happen but it works :) 
						strcpy(current->dataUniq,dataUniqq);
					}
					return 2;
				}				
				current = current->next;	
			}
    nodePtr *newNode = (struct node*)malloc(sizeof(struct node));  
    newNode->count = 1;
	newNode->data = strdup(value);  
	newNode->dataCat = strdup(dirCat);  
	newNode->dataUniq = strdup(dataUniqq);
	newNode->dUCount = 1;
    newNode->next = NULL;  
      
    if(head == NULL) {  // if linked list is empty
        head = newNode;  
        tail = newNode;  
    }  
    else {   
        tail->next = newNode;  
        tail = newNode;
		}  
    return 1;
}  
//End of Function to add new Items for Part B

// Start of Funciton to sort for Part B
 void sortList() {   
        struct node *current = head, *index = NULL;  
        int temp; 
        char tempData[20];
        char tempCat[20];
        char tempDataUniq[20];
        int tempDataUniqC;
          
        if(head == NULL) {  
            return;  
        }  
        else {  
            while(current != NULL) {  
                index = current->next;  
                while(index != NULL) {  
                    if(current->count < index->count) {  //to change their content
                        temp = current->count;  
                       	strcpy(tempData,current->data);
                       	strcpy(tempCat,current->dataCat);
                       	
                        current->count = index->count;  
                        strcpy(current->data,index->data);
                        strcpy(current->dataCat,index->dataCat);
                        
                        index->count = temp;  
                        strcpy(index->data,tempData);
                        strcpy(index->dataCat,tempCat);
                        
                        tempDataUniqC = current->dUCount;
                        current->dUCount = index->dUCount;
                        index->dUCount = tempDataUniqC;
                        
                        strcpy(tempDataUniq,current->dataUniq);
                        strcpy(current->dataUniq,index->dataUniq);
                        strcpy(index->dataUniq,tempDataUniq);
                        
                    }  
                    index = index->next;  
                }  
                current = current->next;  
            }      
        }  
}  
// End of Funciton to sort for Part B    

void printItems(node *r){ // to print for first co-occurence
printf("First Co-occurence : ");
	while(r != NULL){ 
		if(r->firstO != NULL){
			node * firstOccurence = r->firstO;
			while(firstOccurence != NULL){
				printf("{%s,%s}, ",r->data,firstOccurence->data);
				firstOccurence = firstOccurence->firstO;
			}
				
		}	
		r = r->next;
	}
}


void printItemsSecond(node *r){ // to print for second co-occurence
printf("\n\n\n Second Co-Occurence : ");
	while(r != NULL){ 
		if(r->secondO != NULL){
			node * secondOccurence = r->secondO;
			while(secondOccurence != NULL){
				printf("{%s,%s}, ",r->data, secondOccurence->data);
				secondOccurence = secondOccurence->secondO;
			}  
				
		}	
		r = r->next;
	}
}


void printItemsPartB(nodePtr *head, char whichPart[10]){ // for part B and Part C 
//later i added part C"
	
	int j,sum;
	
	for(j=0; j<3;j++)
	{
		nodePtr *c = head; 
		sum = 1;
		
		if(strcmp(whichPart,"B") == 0) 
			printf("Category : %s \n\n",categoryArray[j]);
		else if(strcmp(whichPart,"C") == 0)
			printf("Category : %s ve içindeki dosya sayýsý: %d \n\n",categoryArray[j],categoryTotalFiles[j]);
		
		
		while(c != NULL) {  // tek bir tane while döngüsü var. her 2 yazdýrma iþlemi içinde
		if(strcmp(whichPart,"B") == 0) { // for part B
				if(strcmp(c->dataCat,categoryArray[j])==0 && sum <= 5) // first 5 items for each category
				{
					printf(" \t '%s' -  %d kere \n ", c->data,  c->count);      
					sum++;  		
				}		
		}
		else if(strcmp(whichPart,"C") == 0) { // for part C
				if(strcmp(c->dataCat,categoryArray[j])==0 && sum <= 5) // first 5 items for each category
				{
					printf(" \t '%s' - ", c->data); 
					double logResult = (double)categoryTotalFiles[j] / (double)c->dUCount;
					printf(" %2.2f \n", log(logResult));
					sum++;  		
				}		
		}
        	c = c->next;  
    	}  
   		printf("\n \n");  
	}
 
}


int main(){
	setlocale(LC_ALL, "Turkish");
	root = NULL;
	fileListing();	
	printItems(root); // Master Linked list first co occurencelarý veriyor
	
	// we are finding second cooccurence's words
	findSecondCoOccurence(root);
	
	printItemsSecond(root);
	
	printf("\n \n Part B \n \n");
	sortList(); // baþka bir linked listi kelimelerin tekrar sýrasýna göre sýralýyor.
	printItemsPartB(head,"B"); 
	
		
	printf("\n \n Part C \n \n");
	printItemsPartB(head,"C");
		
}  

//Start of File Listing Function 
fileListing(){
		struct dirent * directory;
		char openUrl[100],str2[50],sentUrl[150]; // sentUrl, to send the full file path to the new function
		int i = 0;
		for( i = 0; i<3; i++){
			int fileSumNumber = 0; // for part C, file number in each category
		 	strcpy(openUrl, dirPath);// dirPath, the name of our home folder
			strcpy(str2,categoryArray[i]); 
	  		strcat(openUrl, str2);
				
			DIR *dir = opendir(openUrl);
			if(dir == NULL){
				printf("Error 101"); // if file is not opened
				return 0;
			}
		
			while ((directory = readdir(dir)) != NULL ) {
				if(strlen(directory->d_name) >2){ // greater than 2 because, Listelerken üstteki dosyalarý ifade eden noktalar geliyordu. 				
					strcpy(sentUrl,openUrl);
					strcat(sentUrl,"/");
					strcat(sentUrl,directory->d_name);
				 	fileOpen(sentUrl,str2,directory->d_name); // we send the full file address and re-category to the new function
					fileSumNumber++;// for part B
				}	
			}	
			closedir(dir);  // its is closed	
		
			categoryTotalFiles[i] = fileSumNumber; // her kategoride ki dosya sayýsýný en üsttteki array dizisine atýyor..
		}	  
	
} 

fileOpen(char url[150], char dirCat[20], char dirName[]){
	
	FILE *targetFile = fopen(url,"r"); // to open url which came to us from the fileListing() function
	if(targetFile == NULL){ //
		printf("File not found."); // if file not found
		return 0;
	} 	
	char fileString[512];
	char *getString;
	char uniqeFileName[150]; // to mediatize file name and file's category so there are uniqe id for every file
	strcpy(uniqeFileName,dirCat); // c kýsmý için her dosya için uniq bir deðer oluþturdum. Ex: econ1.txt bu adda sadece 1 dosya olabilir. 
	dirName = strtok(dirName,".");
	strcat(uniqeFileName,"-");
	strcat(uniqeFileName,dirName);


	while(fgets(fileString,512, targetFile)){
		getString = strtok(fileString," ");
		while(getString != NULL){
			root = sequentialInsert(root, getString,uniqeFileName,dirCat); // to send for part A
			a = addNodeForPartB(getString,dirCat,uniqeFileName);   // to send for part B and uniqeFileName for part C
			getString 	= strtok(NULL," ");
		} 
	} 
	strcat(uniqeFileName,"");
	fclose(targetFile); 
}


