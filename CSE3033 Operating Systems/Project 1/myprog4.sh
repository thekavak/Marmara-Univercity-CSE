largest="./largest"
smallest="./smallest"
largestFile=""
smallestFile=""

if ! [ -d "$largest" ]
then
	mkdir largest
fi

if ! [ -d "$smallest" ]
then
	mkdir smallest
fi
filename=output4.txt
myfile=myprog4.sh

ls *.?* -S>$filename

i=0

while read line
do

		if test line!=$myfile
then
		if test $i -eq 0
		then
		largestFile=$line
		smallestFile=$line
		else
		smallestFile=$line
		fi
fi
i=$(($i+1))


done <$filename
mv $largestFile $largest
mv $smallestFile $smallest

rm $filename

