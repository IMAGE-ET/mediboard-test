#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# System and backups distant synchronisation
########

announce_script "Mediboard synchronisation"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <location> "
  echo "  <location> is the remote location to be rsync-ed, ie root@oxmytto.homelinux.com"
  exit 1
fi
   
location=$1

# Backups directory
rsync -e ssh -avz --delete-after $location:/var/backup /var/
check_errs $? "Failed to rsync Backups directory" "Succesfully rsync-ed Backups directory!"


# system directory
rsync -e ssh -avz --delete-after $location:/var/www/html/mediboard /var/www/html/
check_errs $? "Failed to rsync Mediboard directory" "Succesfully rsync-ed Mediboard directory!"
