#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# System and backups distant synchronisation
########

announce_script "Mediboard synchronisation"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <location> <source directory> <destination>"
  echo "  <source location> <source directory> <destination> is the remote location to be rsync-ed, ie root@oxmytto.homelinux.com"
  echo "  <source directory> is the remote directory to be rsync-ed, /var/www/"
  echo "  <destination> is the target remote location, /var/backup/"
  exit 1
fi
   
location=$1
directory=$2
destination=$3

# Backups directory
rsync -e ssh -avz $location:$directory $destination/$(echo $location | cut -d'@' -f2)
check_errs $? "Failed to rsync Backups directory" "Succesfully rsync-ed Backups directory!"
