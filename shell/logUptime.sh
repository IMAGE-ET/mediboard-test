#!/bin/sh

BASH_PATH=$(dirname $BASH_SOURCE)
. $BASH_PATH/utils.sh

########
# Uptime logger for server load analysis
########

announce_script "Uptime logger"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <file>"
  echo "  <file> is the target for log, ie /var/log/uptime.log"
  exit 1
fi
   
file=$1

## Make the log line
dt=$(date '+%Y-%m-%dT%H:%M:%S'); 
up=$(uptime | awk '{print $10 $11 $12 }'); 

## Log the line
echo "$dt $up" >> $file
check_errs $? "Failed to log uptime" "Uptime logged!"