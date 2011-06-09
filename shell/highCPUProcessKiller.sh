#!/bin/bash
LOGFILE="/var/log/mediboard/kill.high.processes.log"
for i in {1..5}; do
TOP=$( /usr/bin/top -b -n 1 | /bin/grep -A 1 "PID USER" | /bin/grep -v "PID USER" | /usr/bin/tr -s " " )
PID=$( echo ${TOP} | /usr/bin/cut -d' ' -f1 )
CPU=$( echo ${TOP} | /usr/bin/cut -d' ' -f9 )
TIME=$( echo ${TOP} | /usr/bin/cut -d' ' -f11 )
NAME=$( echo ${TOP} | /usr/bin/cut -d' ' -f12 )
MIN=$( echo $TIME | /usr/bin/cut -d':' -f1 )
DATE=$( /bin/date +%Y-%m-%d )
HOUR=$( /bin/date +%H:%M:%S )
if [ $CPU -gt 30 ]; then
if [ "$NAME" == "apache2" ] && [ $CPU -gt 80 ]; then
echo "[${DATE} ${HOUR}] Killing process $PID (${NAME}: ${CPU}%, ${TIME})" >> $LOGFILE
/bin/kill -9 $PID
else
echo "[${DATE} ${HOUR}] High process $PID (${NAME}: ${CPU}%, ${TIME})" >> $LOGFILE
fi
fi
sleep 10
done