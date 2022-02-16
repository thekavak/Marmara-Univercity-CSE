#!/bin/sh

if [ $# -eq 0 ]
then
	echo "$0 You must enter file name with .txt to read data"
	exit 1
fi

while read LINE
do
x=1
	while [ $x -le $LINE ]
	do
		echo -n "*"
		x=$(($x+1))
	done
#newline
echo 
done <$1
