#!/bin/sh

copier()
{
  local PASSED=$1
  if [ -d "${PASSED}" ] ; then
    files=`ls $PASSED`
    for file in $files 
    do
     copier $PASSED/$file
    done
  else
    if [ -f "${PASSED}" ]; then
        echo "${PASSED} is a file";
    else
        echo "${PASSED} is not valid";
        exit 1
    fi
  fi
}

code=$1
prod=$2
files=`ls $code`
for file in $files 
do
 copier $code/$file
done

