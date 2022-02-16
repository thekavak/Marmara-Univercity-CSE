#include <stdio.h>
#include <locale.h> 
#include <string.h>
#include <dirent.h>  
#include <stdlib.h>
#include <math.h>


struct node{
	int value;
  	char fileName[200];
	int degree;
	struct node* parent;
	struct node* child;
	struct node* sibling;
};
typedef struct node Node;
	Node *HH = NULL;
	


char dirPath[20] = "files/"; // path of files
char paramA[100];// parameter entered keyboard
//but the number of those that do not contain the search word
int numberR = 0;//Number of relevant documents 
int numberT=0; //Number of total Documents


void binomialLink ( Node* a, Node* b){
	a->parent = b;
	a->sibling = b->child;
	b->child = a;
	b->degree = b->degree + 1;
}	


//Create a new node to store data
struct node* newNode(int value,char fileName[200]){
	struct node* x;
	x = (struct node*)malloc(sizeof(struct node));
	x->child = x->parent =x->sibling = NULL; 
	x->degree=0;
	strcpy(x->fileName,fileName);//fileName
	x->value = value; // value is frequency of the parameters searched key
	return x;
}

struct node* heapMerge ( Node* H1,  Node* H2){
	//H1=H uniondan gelen
	//H2=H1 yine uniondan gelen
	Node* H = (Node* )malloc(sizeof(Node));
	H=NULL;
	struct node* x= (Node*)malloc(sizeof(Node));
	struct node* y= (Node*)malloc(sizeof(Node));
	struct node* t= (Node*)malloc(sizeof(Node));
	struct node* z= (Node*)malloc(sizeof(Node));
	H=NULL;
	x = H1;
	y = H2;
	
	if(H1==NULL)return H2;
	if(H2==NULL)return H1;

	//Eðer her ikisi de null deðilse
  if (x != NULL && y != NULL) {
	   if (x->degree <= y->degree){
		 	H=x;
		}
		else
			H=y;		
	} 
	
	while(x != NULL && y != NULL) {
		if (x->degree < y->degree){
			x=x->sibling;
		} 
		else if (x->degree == y->degree){
			t=x->sibling;
			x->sibling = y;
			x=t;
		}
		else{
			z=y->sibling;
			y->sibling=x;
			y=z;
		}
	}
	return H;
}

Node* heapUnion( Node* H1,  Node* H2){
	if(H1== NULL && H2==NULL){
		return NULL;
	}
	Node* H = (Node* )malloc(sizeof(Node));
	H = heapMerge(H1, H2);

	Node *prev_x = NULL;
	Node *temp = H;
	Node * next_x=temp->sibling;

  	while (next_x != NULL) {
		if ((temp->degree != next_x->degree) || ((next_x->sibling != NULL) && next_x->sibling->degree == temp->degree)){
			prev_x = temp;	
			temp = next_x;
		} else if (temp->value >= next_x->value){ //Here 
			//Here we are doing max heap here. To keep the biggest ones as root
		  	   temp->sibling = next_x->sibling; 
			   binomialLink(next_x,temp); 
		} else {
		  	if (prev_x == NULL){ 
				H = next_x; 
		    }else{ 
		  		prev_x->sibling = next_x; 
		    }
			binomialLink(temp,next_x); 
			temp= next_x; 
		}
		next_x = temp->sibling;
	}
	return H;
}



/* I used a different method. First I set it up as heap max. Then I found the biggest element by browsing through all max nodes. (Function name: exractMethod)*/
/*Then I removed this max node from the heap. (function name: deleteNode). */
/*Before I deleted this node completely, I put the child of the node into the printNode function and I put all the children and siblings under it back into the heap*/
/* So I removed the max nodes from the heap.*/




//this function checks the siblings and finds the largest of them
int exractMethod(struct node* H) {
	  //printf("\n");
    struct node* p;
    struct node* temp;
    struct node* pre;
    struct node* next;
 	
	int tempMax=0;
	char tempFileName[100];
	
    if (H == NULL) {
        //printf("\n Empty Heap");
        return 0;
    }
    p = H;  
    while (p != NULL) {
       // printf("%d", p->value);
       
        if(p->value >= tempMax)
        {
        	tempMax = p->value;
        	strcpy(tempFileName,p->fileName);
        	temp=p;
		}
        
        //if (p->sibling != NULL)
            //printf("-->");
            
        p = p->sibling;
    }
    //Here too I print the biggest one on the screen
    printf( "Filename:%s (%d)\n",tempFileName,tempMax);
     deleteNode(&HH,tempMax,tempFileName);
     
     
}
//It puts the (largest root) to be deleted here
 deleteNode(struct node** heap, int key, char fileName[200]) {
  struct node *temp = *heap;
  struct node *prev;

  if (temp != NULL && temp->value == key && (strcmp(fileName,temp->fileName) == 0)) {
    *heap = temp->sibling; 
    return;
  }
  // Find the key to be deleted
  while (temp != NULL && temp->value != key) {
    prev = temp;
    temp = temp->sibling;
  }

  if (temp == NULL) return;
  
  prev->sibling = temp->sibling;
 
printNode(temp->child);
//I throw it to the printf function, to add the ones below it back to the heap

 	
}


//I find the one to be deleted from the root, detach it, take its child and add all the underlying elements (childs and siblings) back to the existing list.
 printNode(struct node *n){
    if(n==NULL)
        return;
    struct node* np;
   // printf("%s deðeri %d \n ",n->fileName,n->value);	
 	HH = heapUnion(HH, newNode(n->value,n->fileName)); 
	//printf("%s deðeri%d - sibling %d- cdilf %d\n ",n->fileName,n->value,n->sibling->value,n->child->value);	
    printNode(n->sibling);
    printNode(n->child);
   
}

//Start of File Listing Function 
fileListing(){
	
			struct dirent * directory;
			DIR *dir = opendir(dirPath);
			if(dir == NULL){
				printf("Error 101"); // if file is not opened
				return 0;
			}
			int i = 1;
	
			while ((directory = readdir(dir)) != NULL ) {
				if(strlen(directory->d_name) >2){ // greater than 2 because, Listelerken üstteki dosyalary ifade eden noktalar geliyordu. 				
					fileOpen(directory->d_name);
					numberT++;//Number of total documents
				}	
			}	
			closedir(dir);  // its is closed		
} 

fileOpen(char path[100])// file name
{

	char fullFilePath[200];
	strcpy(fullFilePath,dirPath);// files/
	strcat(fullFilePath,path);//files/content....
	
	FILE *targetFile = fopen(fullFilePath,"r"); // to open url which came to us from the fileListing() function
	if(targetFile == NULL){ //
		printf("File not found."); // if file not found
		return 0;
	} 	
	
	char fileString[512];
	char *getString;
    char word[50];
    
    int count=0;

    if (targetFile == NULL)
        printf("Can't open %s for reading.\n", targetFile);
    else
    {
        while (fscanf(targetFile, "%s", word) != EOF)
        {
          	int init_size = strlen(word);
			char delim[]=".";//because there are .. or: signs between some 2 words
			char delimdot[]=":";
	
			char *ptr1 = strtok(word, delim);
			while(ptr1 != NULL)
			{
					char *ptr= strtok(ptr1, delimdot);
						while(ptr != NULL)
						{
						 int i,j;
					      for (i = 0, j; ptr[i] != '\0'; ++i) {
					      // enter the loop if the character is not an alphabet
					      // and not the null character
					      while (!(ptr[i] >= 'a' && ptr[i] <= 'z') && !(word[i] >= '0' && ptr[i] <= '9') && !(word[i] >= 'A' && ptr[i] <= 'Z')   && !(ptr[i] == '\0')) {
					         for (j = i; ptr[j] != '\0'; ++j) {
					            // if jth element of line is not an alphabet,
					            // assign the value of (j+1)th element to the jth element
					            ptr[j] = ptr[j + 1];
					         }
					         ptr[j] = '\0';
					      }
					   }
					   
					if(strcmp(ptr,paramA) == 0)//paramA
					{
						count++;
						//printf("%s",ptr);
					}
					ptr = strtok(NULL, delimdot);
					}
				ptr1 = strtok(NULL, delim);
				}
		}
		
		
		//it sends the word and frequency to the heap.
		HH = heapUnion(HH, newNode(count,path)); 
		if(count==0)//If there is no search word in that document
		{
			numberR++;
		}
		
        fclose(targetFile);
    }
 
	fclose(targetFile); 
}

int main() 
{ 
	//(upper and lower case letters differ)
	printf("Please Enter a Keyword to Search:");
    scanf("%s", paramA);
	int i,n=5;
	
	//FileListing and add heap
	fileListing();
	printf("Number of Relevant Documents %d\n\n",numberT-numberR);
	
	printf("\n\nRelevance Order is\n");
 
	for(i=0; i<n;i++)
	{
		printf("%d -",i+1);
		exractMethod(HH); 
	}
    
} 
