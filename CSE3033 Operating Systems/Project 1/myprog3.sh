#- Programming Assignment 1 - Question 3
#Aydın Duygu - 150118981
#Ubeydullah Günay - 150117063
#Hamza Kavak - 150118886
#check number of args
if test $# -ne 3
  then
    echo "You should enter a filename and two words as argument!"
    exit 1

fi

filename=$1
word1=$2
word2=$3

numberofsubs=0

while :
  do
    #if word1 exist in file
    if grep -q $word1 $filename 
	then
      #change first occurrence of word1 with word2
      sed -i "s/$word1/$word2/" $filename
      #increase number of substutions by 1
      numberofsubs=$(($numberofsubs+1))
    else
      echo "All $numberofsubs occurrences of \"$word1\" in $filename has changed with \"$word2\""
      exit 0
    fi
  done

if test $? -eq 1
  then
    echo "File not found! Please make sure that you enter correct path!";
    exit 1
fi
