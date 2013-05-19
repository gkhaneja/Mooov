#!/bin/sh

copier()
{
  local SRC=$1
  if [ -d "${SRC}" ] ; then
    files=`ls $SRC`
    for file in $files 
    do
     copier $SRC/$file $2/$file
    done
  else
    if [ -f "${SRC}" ]; then
        #echo "${SRC} is a file";
        if [ -f "${2}" ]; then
         difference=`diff $SRC $2`
         echo "diff $SRC $2 > ../diff"
         echo $difference
        fi
    else
        echo "${SRC} is not valid";
        exit 1
    fi
  fi
}

code=$1
prod=$2
files=`ls $code`
for file in $files 
do
 copier $code/$file $prod/$file
done

