#!/bin/bash

phpExec=$1;
selfpath=$(cd "$(dirname "$0")"; pwd)

function checkprocess(){
    if (ps aux|grep -v grep|grep "$1" )
    then
        echo "active"
    else
        #echo "miss"
        #echo $1
        $phpExec $1 $2 &
    fi
}

function execscript(){
    queueList=`$phpExec "$selfpath/queuelist.php"`
    for queue in $queueList
    do
       checkprocess "$selfpath/queue.php $queue"
    done
}

execscript
