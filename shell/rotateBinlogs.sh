#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

if [ "$#" -lt 5 ]
then
  echo "Usage: $0 <MySQL_username> <MySQL_password> <binlogs_directory> <binlog-index_filename> <backup_directory> options"
  echo " <MySQL_username>  is the MySQL username allowed to connect, ie admin"
  echo " <MySQL_password> is the password of the MySQL user"
  echo " <binlogs_directory>  is the directory where binlogs are stored, ie /var/log/mysql"
  echo " <binlog-index_filename> is the name of the binlog-index file, ie log-bin.index"
  echo " <backup_directory> is the name of the directory where binlogs will be stored, ie /mbbackup/binlogs"
  echo " Options :"
  echo "   [-c <passphrase>] is the passphrase to encrpyt the archive
  echo "   [-e <cryptage>]   is the cryptage method to use
  exit 1
fi

passphrase=''
cryptage='aes-128-cbc'

args=`getopt c:e: $*`
set -- $args
for i; do
  case "$i" in
    -c) passphrase=$2; shift 2;;
    -e) cryptage=$2; shift 2;;
    --) shift ; break ;;
  esac
done

##################
# Rotate binlogs #
##################

announce_script "Rotate binlogs"

# Backup destination dir
backup=$5
mkdir -p $5

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

# Copy binlog indeces to binlog backup
cp $index $backup

date=`date '+%Y-%m-%dT%H:%M:%S'`

# Archive binlogs
if [ -n "$passphrase" ]; then
  tar -vcjf - -C $backup $backup/*bin.0* | openssl $cryptage -salt -out $backup/binlogs_$date.tar.bz2.aes -k $passphrase
else
  tar -vcjf $backup/binlogs_$date.tar.bz2 -C $backup $backup/*bin.0*
fi

# Rotate binlogs and indeces for a week
find $backup -name "*bin.0*" -exec rm -f {} \;

if [ -n "$passphrase" ]; then
  find $backup -name "binlogs_*.tar.bz2.aes" -mtime +7 -exec rm -f {} \;
else
  find $backup -name "binlogs_*.tar.bz2" -mtime +7 -exec rm -f {} \;
fi