#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Replace mediboard database
########

announce_script "Mediboard replace base"

if [ "$#" -lt 5 ]
then 
  echo "Usage: $0 <source_location> <source_directory> <source_database> <target_database> <port>"
  echo " <source_location>  is the remote location to be rsync-ed, ie root@oxmytto.homelinux.com"
  echo " <source_directory> is the remote directory to be rsync-ed, /var/www/"
  echo " <destination>      is the target remote location, /var/backup/"
  echo " <source_database>  is the source database name, ie mediboard"
  echo " <target_database>  is the target database name, ie target_mediboard"
  echo " <safe> (optionnal) is the copy source database "
  echo " <port> (optionnal) is the ssh port af the target remote location, 22"
  exit 1
fi

source_location=$1
source_directory=$2
destination=$3
if [ $4 ]
then
  source_database=$4
else
  source_database=mediboard
fi
if [ $5 ]
then
  target_database=$5
else
  target_database=mediboard
fi
safe=$6
if [ $7 ]
then
  port=$7
else
  port=22
fi

# Mysql Path
path=/etc/init.d/mysql
if [ -f "$path" ]
then 
  mysql_path=/etc/init.d/mysql
else
  mysql_path=/etc/init.d/mysqld
fi

# Retrieve archive 
scp $source_location:$source_directory/$source_database-latest.tar.gz $destination/$(echo $location | cut -d'@' -f2)
check_errs $? "Failed to retrieve archive" "Succesfully retrieve archive!"

# Extract base
cd $destination
tar -xvf $source_database-latest.tar.gz
check_errs $? "Failed to extract files" "Succesfully extracted files"

# Stop mysql
"$mysql_path" stop
check_errs $? "Failed to stop mysql" "Succesfully stop mysql"

dir_target=/var/lib/mysql/$target_database

if [ $5 ]
then
  DATETIME=$(date +%Y-%m-%dT%H-%M-%S)
  # Copy database
  mv $dir_target $dir_target-$DATETIME
  mkdir $dir_target
  chown mysql $dir_target
  chgrp mysql $dir_target
else
  # Delete files in mediboard database
  rm -f $dir_target/*
  check_errs $? "Failed to delete files" "Succesfully deleted files"
fi

# Transfer files 
cd $source_database
mv * $dir_target
check_errs $? "Failed to move files" "Succesfully moved files"

# Change owner & group 
cd $dir_target
chown mysql *
chgrp mysql *
check_errs $? "Failed to change owner and group" "Succesfully changed owner and group"

# Start mysql
"$mysql_path" start
check_errs $? "Failed to start mysql" "Succesfully start mysql"

rm -rf $destination/$source_database
rm $destination/$source_database-latest.tar.gz
check_errs $? "Failed to delete archive" "Succesfully deleted archive"