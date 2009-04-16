#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard BCB updater
########

announce_script "Mediboard BCB updater"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <httpword> <database> <username> <password>
  echo "  <httpword> is the Mediboard portal password to access bcb/ folder
  echo "  <database> is the name of the database you want to empty and fill in
  echo "  <username> is the the database username
  echo "  <password> is the the database password
  exit 1
fi
   
httpword=$1
database=$2
username=$3
password=$4

