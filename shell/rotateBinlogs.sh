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
  echo "   [-c <passphrase>] is the passphrase to encrpyt the archive"
  echo "   [-e <cryptage>]   is the cryptage method to use"
  exit 1
fi

passphrase=''
cryptage='aes-128-cbc'

args=$(getopt c:e: $*)

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

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

# Tmp dir to compress
dir=$3
tmpdir=$3/tmp_binlogs
mkdir -p $tmpdir

info_script "Flush logs to start a new one"
# Flush logs to start a new one
mysqladmin -u $1 -p$2 flush-logs

# Move all logs without latest to tmp dir
info_script "Move all logs without latest to tmp dir"
index="$dir/$4"
last=$(tail -n 1 $index)
for log in $dir/*bin.0* ; do
  if [ "$log" != "$last" ]; then
    info_script "Moving $(ls -sh $log)"
    mv $log $tmpdir
  fi
done
# Copy binlog indeces to binlog backup
info_script "Copying binlog indeces to binlog backup"
cp $index $backup

date=$(date '+%Y-%m-%dT%H:%M:%S')

# Archive binlogs
cd $tmpdir
if [ -n "$passphrase" ]; then
  info_script "Compress binlogs"
  nice -n 10 tar -vczf - *bin.0* | openssl $cryptage -salt -out $tmpdir/binlogs_$date.tar.gz.aes -k $passphrase
  info_script "Moving compressed binlogs to $backup"
  mv $tmpdir/binlogs_$date.tar.gz.aes $backup
else
  info_script "Compress binlogs"
  nice -n 10 tar -vczf $tmpdir/binlogs_$date.tar.gz *bin.0*
  info_script "Moving compressed binlogs to $backup"
  mv $tmpdir/binlogs_$date.tar.gz $backup
fi

# Remove temp directory
info_script "Remove temp directory"
cd ..
rm -rf $tmpdir

# Rotate binlogs for a week
info_script "Rotating binlogs for a week"

if [ -n "$passphrase" ]; then
  find $backup -name "binlogs_*.tar.gz.aes" -mtime +7 -exec rm -f {} \;
else
  find $backup -name "binlogs_*.tar.gz" -mtime +7 -exec rm -f {} \;
fi