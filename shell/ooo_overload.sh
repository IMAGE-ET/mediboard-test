#!/bin/sh
# Openoffice memory leak...
# If over 10%, restart it.

percent=$(ps -aux|grep soffice.bin|grep headless|sed -r 's/\s+/\ /g'|cut -d" " -f4|cut -d"." -f1)
force_restart=$1

if [ $percent -ge 10 ] || [ "$force_restart" = "1" ]
then
  pkill soffice;
  export HOME=/tmp; /usr/bin/soffice --accept="socket,host=localhost,port=8100;urp;StarOffice.ServiceManager" --nologo --headless --nofirststartwizard --norestore >> /tmp/log_ooo &
fi

echo $percent