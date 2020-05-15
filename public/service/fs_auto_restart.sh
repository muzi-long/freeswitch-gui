#!/bin/sh
while true
do
	ps -ef | grep "bin/freeswitch" | grep -v "grep"
	if [ $? -eq 0  ] 
	then
		echo "$?"
		echo "freeswitch process already started!"
	else
	    supervisorctl stop callcenter-run:*
		echo "$?"
		/usr/local/freeswitch/bin/freeswitch -u www -g www -ncwait
		echo "freeswitch process has been restarted!"
		sleep 1
		echo "restart asr"
		supervisorctl restart esl-listen:
		supervisorctl restart callcenter-listen:*
		supervisorctl start callcenter-run:*
	fi
	sleep 5

done
