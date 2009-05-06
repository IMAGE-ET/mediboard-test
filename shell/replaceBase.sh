#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Replace mediboard database
########

announce_script "Mediboard replace base"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <source_location> <source_directory> <destination> <port>"
  echo " <source_location>  is the remote location to be rsync-ed, ie root@oxmytto.homelinux.com"
  echo " <source_directory> is the remote directory to be rsync-ed, /var/www/"
  echo " <destination>      is the target remote location, /var/backup/"
  echo " <port> (optionnal) is the ssh port af the target remote location, 22"
  echo " <database>         is the database name, ie mediboard"
  exit 1
fi

source_location=$1
source_directory=$2
destination=$3
if [ $4 ]
then
  port=$4
else
  port=22
fi
if [ $5 ]
then
  database=$5
else
  database=mediboard
fi

# Retrieve archive 
scp $source_location:$source_directory/latest $destination/$(echo $location | cut -d'@' -f2)
check_errs $? "Failed to retrieve archive" "Succesfully retrieve archive!"

# Stop mysql
/etc/init.d/mysql stop
check_errs $? "Failed to stop mysql" "Succesfully stop mysql"

# Delete files in mediboard database
rm -f /var/lib/mysql/$database/*
check_errs $? "Failed to delete files" "Succesfully deleted files"

# Extract base
cd $destination
tar -xvf latest
check_errs $? "Failed to extract files" "Succesfully extracted files"

# Transfer files 
cd $database
mv * /var/lib/mysql/$database
check_errs $? "Failed to move files" "Succesfully moved files"

rm -rf $destination/$database
rm $destination/latest
check_errs $? "Failed to delete archive" "Succesfully deleted archive"

# Change owner & group 
cd /var/lib/mysql/$database
chown mysql *
chgrp mysql *
check_errs $? "Failed to change owner and group" "Succesfully changed owner and group"

# Start mysql
/etc/init.d/mysql start
check_errs $? "Failed to start mysql" "Succesfully start mysql"