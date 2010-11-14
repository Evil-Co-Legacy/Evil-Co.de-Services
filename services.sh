#!/bin/bash
if [ test $1 ]; then
	if [ "$1" == "start" ]; then
		if [ test -e ./services.pid ]; then
			echo "Error: The application runs already!"
			echo "If you're sure that the application does not run delete the file services.pid!"
		else
			echo "Starting services ..."
			php ./start.php & >> /dev/null
			echo "Started!"
		fi
	fi
	
	if [ "$1" == "stop" ]; then
		if [ test -e ./services.pid ]; then
			echo "Stopping services ..."
			SERVICESPID=`cat ./services.pid`
			kill $SERVICESPID
			echo "Stopped!"
		else
			echo "Services aren't started"
			echo "No pidfile found!"
		fi
	fi
	
	if [ "$1" == "restart" ]; then
		./$0 stop
		./$0 start
	fi
else
	echo "Syntax: $0 (start|stop|restart)"
fi