#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard request launcher
########

announce_script "Mediboard request launcher"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <root_url> <username> <password> \"<param>\" \[times\] \[delay\]"
  echo "  <root_url> is root url for mediboard, ie https://localhost/mediboard"
  echo "  <username> is the name of the user requesting, ie cron"
  echo "  <password is the password of the user requesting, ie ****"
  echo "  <params> is the GET param string for request, ie m=dPpatients&tab=vw_medecins"
  echo "  [-t <times>] is the number of repetition, ie 4"
  echo "  [-d <delay>] is the time between each repetition, ie 2"
  echo "  [-f <file>] is the file for the output, ie log.txt"
  exit 1
fi
   
root_url=$1
login="login=1"
user=username=$2
pass=password=$3
params=$4
file=""
times=1

while getopts t:d:f: option
do
  case $option in
    t)
      times=$OPTARG
      ;;
    d)
      delay=$OPTARG
      ;;
    f)
      file='-O $OPTARG'
      ;;
  esac
done
echo $file
url="$root_url/index.php?$login&$user&$pass&$params"

# Make mediboard path
MEDIBOARDPATH=/var/log/mediboard
force_dir $MEDIBOARDPATH

log=$MEDIBOARDPATH/jobs.log
force_file $log

mediboard_request() 
{
   wget $url\
        --append-output="$log"\
        --force-directories\
        --no-check-certificate\
        file
   check_errs $? "Failed to request to Mediboard" "Mediboard requested!"   
   echo "wget URL : $url."
}

if [ $times -gt 1 ]
then
  while [ $times -gt 0 ]
  do
    times=$(($times - 1))
    mediboard_request &
    sleep $delay
  done
else
  mediboard_request
fi