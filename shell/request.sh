#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard request launcher
########

announce_script "Mediboard request launcher"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <root_url> <username> <password> \"<param>\"" \[times\] \[delay\]
  echo "  <root_url> is root url for mediboard, ie https://localhost/mediboard"
  echo "  <username> is the name of the user requesting, ie cron"
  echo "  <password is the password of the user requesting, ie ****"
  echo "  <params> is the GET param string for request, ie m=dPpatients&tab=vw_medecins"
  echo "  [<times>] is the number of repetition, ie 4"
  echo "  [<delay>] is the time between each repetition, ie 2"
  exit 1
fi
   
root_url=$1
login=login=1
user=username=$2
pass=password=$3
params=$4
times=$5
delay=$6

url="$root_url/index.php?$login&$user&$pass&$params"

# Make mediboard path
MEDIBOARDPATH=/var/log/mediboard
force_dir $MEDIBOARDPATH

log=$MEDIBOARDPATH/jobs.log
check_file_exist $log

mediboard_request() 
{
   time -p --output="$log" --append wget $url\
        --append-output="$log"\
        --force-directories\
        --no-check-certificate
   echo "wget URL : $url."
   check_errs $? "Failed to request to Mediboard" "Mediboard requested!"   
}

if [ "$#" -ne 6 ]
then
  mediboard_request
else
while [ $times -gt 0 ]
do
  times=$(($times - 1))
  mediboard_request &
  sleep $delay
done
fi