#!/bin/sh
if [ $# -eq 0 ] ; then
    echo 'Please enter a branch name'
    exit 1
fi
if [ $# -eq 1 ] ; then
    echo 'Not updating databases'
	echo $1
	branch=$1;
	gitup $branch "update";
	ssh -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch";
fi

if [ $# -eq 2 ] ; then
    echo 'Going to full install'
        echo $1
        branch=$1;
        gitup $branch "update";
        ssh -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch something";
fi


if [ $# -eq 3 ] ; then
    echo 'Going to update databases and full install'
	echo $1
	branch=$1;
	gitup $branch "update";
	ssh -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch something something";    
fi


