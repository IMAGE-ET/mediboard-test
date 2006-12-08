#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard request launcher
########

announce_script "Mediboard request launcher"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <url> <username> <password> \"<param>\""
  echo "  <url> is root url for mediboard, ie https://localhost/mediboard"
  echo "  <username> is the name of the user requesting, ie cron"
  echo "  <password is the password of the user requesting, ie ****"
  echo "  <params> is the GET param string for request, ie m=dPpatients&tab=vw_medecins"
  exit 1
fi
   
url=$1
user=$2
pass=$3
params="$4"

dir=/var/www/html/cron
file=$dir/cron.html
cookie=$dir/cookie.txt

mediboard_connect() 
{
  curl $url/index.php \
    --output $file \
    --create-dirs \
    --insecure \
    --data login=ok \
    --data username=$user \
    --data password=$pass \
    --cookie-jar $cookie \
    --cookie $cookie
  check_errs $? "Failed to connect to Mediboard" "Connected to Mediboard!"
}

mediboard_request() 
{
  curl $url/index.php?$params\
    --output $file \
    --create-dirs \
    --insecure \
    --cookie $cookie
  check_errs $? "Failed to request to Mediboard" "Mediboard requested!"
}

rm -f $file
check_errs $? "Failed to remove output file" "Output file removed!"

mediboard_connect
mediboard_request