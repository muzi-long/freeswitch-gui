#!/bin/sh
while true
do
	ps -ef | grep "bin/freeswitch" | grep -v "grep"
	if [ $? -eq 0  ]
	then
		echo "$?"
		echo "freeswitch process already started!"
	else
		echo "$?"
		/usr/local/freeswitch/bin/freeswitch -u www -g www -ncwait
		echo "freeswitch process has been restarted!"
		sleep 1
		echo "restart asr"
		supervisorctl restart esl-listen:
		supervisorctl restart esl-cdr:*
		supervisorctl restart esl-custom:
		supervisorctl restart callcenter:
	fi
	sleep 5

done
