#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Replace mediboard database
########

announce_script "Mediboard replace base"

if [ "$#" -lt 5 ]
then 
  echo "Usage: $0 <source_location> <source_directory> <source_database> <target_directory> <target_database> (<with_restart>) (<safe>) (<port>)"
  echo " <source_location>  is the remote location, ie root@oxmytto.homelinux.com"
  echo " <source_directory> is the remote directory, /var/backup/mediboard"
  echo " <source_database>  is the source database name, ie mediboard"
  echo " <target_directory> is the target directory location, /var/backup/"
  echo " <target_database>  is the target database name, ie target_mediboard"
  echo " [-r <with_restart>]  is restart the Mysql server (Warning), ie for InnoDB"
  echo " [-s <safe>] is the copy source database "
  echo " [-p <port>] is the ssh port af the target remote location, 22"
  exit 1
fi

port=22
with_restart=0
safe=0
args=`getopt p:rs $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args
for i; do
  case "$i" in
    -r) with_restart=1; shift;;
    -s) safe=1; shift;;
    -p) port=$2; shift 2;;
    --) shift ; break ;;
  esac
done

source_location=$1
source_directory=$2
source_database=$3
target_directory=$4
target_database=$5

if [ $with_restart -eq 1 ]
then
echo "Warning !!!!!!!!!!!! This will restart the MySQL server"
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
archive="archive.tar.gz"
scp $source_location:$source_directory/$source_database-latest.tar.gz $target_directory/$archive
check_errs $? "Failed to retrieve archive" "Succesfully retrieve archive!"

# Extract base
cd $target_directory
tar -xvf $archive
check_errs $? "Failed to extract files" "Succesfully extracted files"

# Stop mysql
if [ $with_restart -eq 1]
then
"$mysql_path" stop
check_errs $? "Failed to stop mysql" "Succesfully stop mysql"
fi

dir_target=/var/lib/mysql/$target_database

if [ $safe -eq 1]
then
  DATETIME=$(date +%Y_%m_%dT%H_%M_%S)
  # Copy database
  mv $dir_target ${dir_target}_$DATETIME
  check_errs $? "Move mysql target directory" "Succesfully move mysql target directory"
  mkdir $dir_target
  check_errs $? "Failed to create mysql target directory" "Succesfully create mysql target directory"
  chown mysql $dir_target
  chgrp mysql $dir_target
  check_errs $? "Failed to change owner and group" "Succesfully changed owner and group"
else
  # Delete files in mediboard database
  rm -f $dir_target/*
  check_errs $? "Failed to delete files" "Succesfully deleted files"
fi

# Move table files 
cd $source_database
mv * $dir_target
check_errs $? "Failed to move files" "Succesfully moved files"

# Change owner & group 
cd $dir_target
chown mysql *
chgrp mysql *
check_errs $? "Failed to change owner and group" "Succesfully changed owner and group"

# Start mysql
if [ $with_restart -eq 1]
then
"$mysql_path" start
check_errs $? "Failed to start mysql" "Succesfully start mysql"
fi

rm -rf $target_directory/$source_database
rm $target_directory/$archive
check_errs $? "Failed to delete archive" "Succesfully deleted archive"
