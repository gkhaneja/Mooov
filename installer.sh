#!/bin/sh
prod='/var/www/html/'
code='/var/www/Mooov/trunk'
files=`ls $code`
echo $files
sample

function sample() {
for file in $files 
do
 echo $file
done
 
}
