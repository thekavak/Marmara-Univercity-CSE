#- Programming Assignment 1 - Question 2
#Aydın Duygu - 150118981
#Ubeydullah Günay - 150117063
#Hamza Kavak - 150118886


#check number of args
if test $# -eq 0
  then
    echo "You should enter the directoryname as argument!"
    exit 1
elif [[ $# -gt 1 ]]; then
    echo "Oops! Too many arguments, enter just 1 directoryname"
    exit 1
fi

directoryname=$1

cd $directoryname

if test $? -eq 1
  then
    echo "Directory not found! Please make sure that you enter correct path!";
    exit 1
fi



filename=files_in_dir.txt
ls -p -I "*.c" -I "*.h" -I "makefile*" -I "Makefile*" -I "$filename"> $filename

while read line;
do

  rm -f "$line"

done<$filename

rm -f "$filename"
echo "All files other than .c files .h files and files with name \"Makefile\" or \"makefile\" are removed"
