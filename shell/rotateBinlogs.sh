#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

if [ "$#" -ne 4 ]
then
  echo "Usage: $0 <MySQL_username> <MySQL_password> <binlogs_directory> <binlog-index_filename>"
  echo " <MySQL_username>  is the MySQL username allowed to connect, ie admin"
  echo " <MySQL_password> is the password of the MySQL user"
  echo " <binlogs_directory>  is the directory where binlogs are stored, ie /var/log/mysql"
  echo " <binlog-index_filename> is the name of the binlog-index file, ie log-bin.index"
  exit 1
fi

##################
# Rotate binlogs #
##################

announce_script "Rotate binlogs"

# Backup destination dir
backup="/mbbackup/binlogs"
mkdir -p /mbbackup/binlogs

# Flush logs to start a new one
mysqladmin -u $1 -p$2 flush-logs

# Move all logs but latest to backup
dir="$3"
index="$dir/$4"
last=$(tail -n 1 $index)
for log in $dir/*bin.0* ; do
  if [ "$log" != "$last" ]; then
    mv $log $backup
  fi
done

# Move binlog indeces to binlog backup
mv $index $backup

date=`date '+%Y-%m-%dT%H:%M:%S'`

# Archive binlogs
tar -vcjf $backup/binlogs_$date.tar.bz2 $backup/*bin.0*

# Rotate binlogs and indeces for a week
find $backup -name "*bin.0*" -exec rm -f {} \; 
         
