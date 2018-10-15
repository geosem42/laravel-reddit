#!/bin/sh
if [ $# -eq 0 ] ; then
    echo 'Please enter a branch name'
    exit 1
fi
chmod 400 ./sshKey
branch=$1;
comment=$2;
if [ $# -eq 1 ] ; then
    echo 'Please enter a comment, you entered this for branch name:'
	echo $1;
    exit 1
fi

if [ $# -eq 2 ] ; then
        echo 'Not updating databases'
	./gitup $branch $comment ;
	ssh -i "./sshKey" -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch";
fi

if [ $# -eq 3 ] ; then
    echo 'Going to full install'
	echo $1
	./gitup $branch $comment;
	ssh -i "./sshKey" -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch something";    
fi

if [ $# -eq 4 ] ; then
    echo 'Going to full install and reset databases'
        ./gitup $branch $comment;
        ssh -i "./sshKey" -t root@poster.projectoblio.com "cd laravel-irt; ./localUpdate.sh $branch something something2"
fi



