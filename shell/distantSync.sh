#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# System and backups distant synchronisation
########

announce_script "Mediboard synchronisation"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <location> <source directory> <destination> \[<port>\]"
  echo "  <source location>  is the remote location to be rsync-ed, ie root@oxmytto.homelinux.com"
  echo "  <source directory> is the remote directory to be rsync-ed, /home/root/"
  echo "  <destination>      is the target remote location, /var/backup/"
  echo "  [<port>]           is the ssh port af the target remote location, 22"
  exit 1
fi
   
location=$1
directory=$2
destination=$3
if [ $4 ]
then
  port=$4
else
  port=22
fi

# Backups directory
rsync -e "ssh -p $port" -avz $location:$directory $destination/$(echo $location | cut -d'@' -f2)
check_errs $? "Failed to rsync Backups directory" "Succesfully rsync-ed Backups directory!"
