#value=0
tempFirst=0
tempSecond=0
totalValue=0

if ! [[ $1 == ?(-)+([0-9]) ]]
then
	echo "it is not number" 
	exit 1
elif [ $# -eq 0 ]
then
	echo "$0 You must enter a positive integer value" 
	exit 1
else
	for ((i=0; i < ${#1}-1; i++ )) do

		tempFirst=(${1:$i:1})
		tempSecond=(${1:$i+1:1})
		totalValue=$((totalValue + (tempSecond * 10) + tempFirst))
		#totalValue=$((totalValue+value))

		#echo $value
	done

	echo $totalValue
	exit 0

fi

