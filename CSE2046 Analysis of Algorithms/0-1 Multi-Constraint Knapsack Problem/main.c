#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <sys/types.h>
#include <dirent.h>
#include <unistd.h>
#include <errno.h>
#include <math.h>

typedef struct Item Item;

struct Item{
    int id;
    int value;
    double profit;
    int* weights;
    int* flag;
    int okay;

};


//we calculated their profit using: geometrical mean of value / capacity
void calculateItems(Item* items, int numberOfItems,int numberOfKnapsacks,int** knapsackCapacities){

    int i;
    for (i=0;i<numberOfItems;i++){
    		 double temp = 1;

        int j;

        for (j=0;j<numberOfKnapsacks;j++){
          
            
             if(items[i].weights[j]== 0)
             {
             	temp = temp * ((double)items[i].value)/((double)((*knapsackCapacities)[j]));
              
			 }else{
			 	temp = temp *  (((double)items[i].value / ((double)((*knapsackCapacities)[j]))));
			
			 }
        }
        temp=pow(temp,1.0/numberOfKnapsacks);

        items[i].profit = (double)temp;
        
       
    }
}

//We listed all items with their flags value.  
void printItems(Item* items, int numberOfItems,int numberOfKnapsacks){

    int i;
    for (i=0;i<numberOfItems;i++){
    		 double temp = 1;
        printf("Item %d\nValue: %d profit %f \nWeights : ",items[i].id,items[i].value,items[i].profit );

        int j;

        for (j=0;j<numberOfKnapsacks;j++){
            printf("- %d - flag %d",items[i].weights[j],items[i].flag[j]);
        }
        
        puts("\n");
    }
}



//First we writed total value at first line
//then we printed 1 for chosen items otherwise 0
void fileWrite(Item* items, int total,int numberOfItems)
{
  FILE *fptr;

   if ((fptr = fopen("output.txt","w")) == NULL){
       printf("Error! opening file");

       // Program exits if the file pointer returns NULL.
       exit(1);
   }

  	fprintf(fptr, "%d\n",total);
    printf("Total Value In The Knapsack: %d\n",total);
    int i,j;
  
    for (j=1;j<=numberOfItems;j++){
    	
	    for (i=0;i<numberOfItems;i++){
	      
	        if(items[i].id == j){
	        	
	           if(items[i].okay == 1){
	        	fprintf(fptr, "%d",1);	
				}else{
					fprintf(fptr, "%d",0);
				}
				
				if(i<numberOfItems)
				{
						fprintf(fptr, "\n");
				}
				
	        	
			}
	        
	    }
    }
 

   fclose(fptr); 	
}

//It checks whether the item to be added to the bag fits in all the bags.
int doesItemFit(Item* item,int numberOfKnapsacks,int* filledParts,int* knapsackCapacities){

    int i;
    for ( i = 0; i <numberOfKnapsacks ; i++) {

        if ((filledParts[i]+(item->weights[i]))>knapsackCapacities[i]){return 0;}

    }

    return 1;
}

//We find the total value as recursive and it prints this item on the console screen.
void findTotal(Item* items, int numberOfItems,int numberOfKnapsacks,int numberOfKnapsacks2,int* totalValue,int* filledParts,int* knapsackCapacities){

    int i;
    int j;
    int sum = 0;
    int tempSum = 0;
    if (numberOfKnapsacks==0){return;}

    for (i=0;i<numberOfItems;i++){
        for (j=0;j<numberOfKnapsacks2;j++){
            sum = sum + items[i].flag[j];
        }
        if(sum == numberOfKnapsacks){

            if (doesItemFit(&items[i],numberOfKnapsacks2,filledParts,knapsackCapacities)){

                *totalValue = *totalValue +	items[i].value;
                items[i].okay = 1;
                printf("Item %d selected\n",items[i].id);
                int k;
                for ( k = 0; k < numberOfKnapsacks2; ++k) {
                    filledParts[k]+=items[i].weights[k];
                }
            }
        }
        sum = 0;
    }
    findTotal(items,numberOfItems,--numberOfKnapsacks,numberOfKnapsacks2,totalValue,filledParts,knapsackCapacities);
}


//If an item fits in that bag, we make its flag 1 for the bag it fits.
void flagSignItems(Item* items, int numberOfItems,int numberOfKnapsacks,int** knapsackCapacities){

    int i,j;
    for (i=0;i<numberOfKnapsacks;i++){
    	int temp = 0;
    	
    	for (j=0;j<numberOfItems;j++){
    		
    		if(items[j].weights[i] + temp <= (*knapsackCapacities)[i])
    		{
    			temp = temp + items[j].weights[i];
    		 	items[j].flag[i] = 1;
    			
			}

    	
    	}
    	
    }
}

//We sorted the array in descending order to find the most valuable item first.
void desSortItems(Item* items, int numberOfItems,int numberOfKnapsacks){
	
	int  i, j;
	Item *temp;
	int itemSize=sizeof (Item)+numberOfKnapsacks*sizeof (int);
 	temp=calloc(1,itemSize);
 	(temp->weights)=(int*)calloc(numberOfKnapsacks,sizeof (int));
    (temp->flag)=(int*)calloc(numberOfKnapsacks,sizeof (int));
 	
	for (i = 0; i < numberOfItems; i++)
	{
		for (j = i + 1; j < numberOfItems; j++)
		{
			if(items[i].profit < items[j].profit)
			{
				temp->profit = items[i].profit;
				temp->id=  items[i].id;
				temp->value =  items[i].value;
				temp->flag =  items[i].flag;
				temp->weights =  items[i].weights;
				
				
				items[i].profit = items[j].profit;
				items[i].id = items[j].id;
				items[i].value = items[j].value;
				items[i].flag = items[j].flag;
				items[i].weights = items[j].weights;
				
				
				
				items[j].profit = temp->profit;
				items[j].id = temp->id;
				items[j].value = temp->value;
				items[j].flag = temp->flag;
				items[j].weights = temp->weights;
			}
			
		}
	}
}

//readfile values,weights and capacities and build structs
void readFile(Item** items,int* numberOfKnapsacks,int* numberOfItems,int** knapsackCapacities){

    char pathname[1024];
    FILE* file = NULL;
    char * end;
  
	
    while (!file){
  
	    puts("Enter the filepath!");
	    scanf("%s",&pathname);
    
        file=fopen(pathname, "r");
        
		if(file!=NULL){
			break;	 
		}
		else{
			puts("Enter the filepath correctly \n");
		}
	
    }

    int n=0;
    int k=0;
    int i=0;
    int x,y;
    int temp = 0;
    int c = 0;


    char line[1024];
    while (fgets(line, sizeof(line), file)) {

        if (i==0){

            *numberOfKnapsacks=strtol(line, &end, 10);
            *numberOfItems=strtol(end, &end, 10);
            x=(*numberOfItems)/10;
            if ((*numberOfItems)%10== 0){x--;};
            y=(*numberOfKnapsacks)/10;
            if ((*numberOfKnapsacks)%10== 0){y--;};
            *knapsackCapacities=(int*)calloc(*numberOfKnapsacks,sizeof(int));
            int j;
            int itemSize=sizeof (Item)+*numberOfKnapsacks*sizeof (int);
            *items=calloc(*numberOfItems,itemSize);
            
		//	printf(" number of numberOfItems %d \n",*numberOfItems);
			
            for(j=0;j<*numberOfItems;j++){
                (*items)[j].id=j+1;
                (*items)[j].value=0;
                 (*items)[j].okay=0;
                ((*items)[j].weights)=(int*)calloc(*numberOfKnapsacks,sizeof (int));
                 ((*items)[j].flag)=(int*)calloc(*numberOfKnapsacks,sizeof (int));

            }
        }

        else if (i<=(x+1)){
            char* end2=line;

            while(strcmp(end2,"\n")!=0){
                (*items)[k].value=strtol(end2,&end2,10);
                
                k++;
            }

        }
        
        

        else if(i<=x+2+y){
        	
		//printf("knaa %d \n",(*numberOfKnapsacks/10));
            char* end2=line;
            while(strcmp(end2,"\n")!=0){
                if (strcmp(end2,"")==0)break;
                
            	  (*knapsackCapacities)[c]=strtol(end2,&end2,10);
                  //printf(" aaas %d \n",  (*knapsackCapacities)[c]);
                  c++;
			}
            /*for (c=0;c<*numberOfKnapsacks;c++){

              

            }*/
        }

        else{
            char* end2=line;
         
            
            while(strcmp(end2,"\n")!=0){
		
                if (strcmp(end2,"")==0)break;
                
                    (*items)[n%*numberOfItems].weights[n/(*numberOfItems)]=strtol(end2,&end2,10);
                    //flag � 0 yapt�m burada
                    (*items)[n%*numberOfItems].flag[n/(*numberOfItems)] = 0;
                
                	n++;
                	
                temp++;
            }
            
        }

        i++;
    }
    fclose(file);

}

int main() {

    int numberOfKnapsacks=0;
    int numberOfItems=0;
    Item *items;
    int* knapsackCapacities;
    int totalValue=0;

    readFile(&items,&numberOfKnapsacks,&numberOfItems,&knapsackCapacities);

    calculateItems(items,numberOfItems,numberOfKnapsacks,&knapsackCapacities);
	desSortItems(items,numberOfItems,numberOfKnapsacks);

	flagSignItems(items,numberOfItems,numberOfKnapsacks,&knapsackCapacities);
	
	printItems(items,numberOfItems,numberOfKnapsacks);

	int filledParts[numberOfKnapsacks];

	int i;
    for ( i = 0; i < numberOfKnapsacks; ++i) {
        filledParts[i]=0;
    }

	findTotal(items,numberOfItems,numberOfKnapsacks,numberOfKnapsacks,&totalValue,filledParts,knapsackCapacities);
    fileWrite(items,totalValue,numberOfItems);

}
