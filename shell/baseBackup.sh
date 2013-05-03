#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Backups database on a daily basis
########

announce_script "Database daily backup"

if [ "$#" -lt 5 ]
then 
  echo "Usage: $0 <method> <username> <password> <database> <backup_path> options"
  echo " <method> is hotcopy or dump method"
  echo " <username> to access database"
  echo " <password> authenticate user"
  echo " <database> to backup, eg mediboard"
  echo " <backup_path> is the backup path, eg /var/backup"
  echo " Options:"
  echo "   [-t <time>] is time in days before removal of files, default 7"
  echo "   [-b ] to create mysql binary log index"
  echo "   [-l <login>] user:pass login to send a mail when diskfull is detected"
  echo "   [-f <lock_file>] lock file path"
  echo "   [-c <passphrase>] passphrase to encrypt the archive"
  echo "   [-e <cryptage>] cryptage method to use"
  exit 1
fi

login=''
time=7
binary_log=0
lock=''
passphrase=''
cryptage='aes-128-cbc'
args=`getopt t:l:f:c:e:b $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args

for i; do
  case "$i" in
    -t) time=$2; shift 2;;
    -l) login=$2; shift 2;;
    -f) lock=$2; shift 2;;
    -c) passphrase=$2; shift 2;;
    -e) crptage=$2; shift 2;;
    -b) binary_log=1; shift;;
    --) shift ; break ;;
  esac
done

method=$1
username=$2
password=$3
database=$4
backup_path=$5

info_script "Backuping '$database' database"

## Make complete path

# Make shell path
SHELL_PATH=`pwd`/$BASH_PATH

# Create lock file
if [ -n "$lock" ]
then
  touch $lock
fi

event=$BASH_PATH/../tmp/svnevent.txt

# Make backup path
BACKUP_PATH=$5
force_dir $BACKUP_PATH

# Make database path
BASE_PATH=${BACKUP_PATH}/$database-db
force_dir $BASE_PATH
cd ${BASE_PATH}

## If no enough free disk space (1.5 * size of database), send mail if provided and quit
mysql_conf=`find /etc -name my.cnf 2>/dev/null|head -n 1`

mysql_data_root=`cat $mysql_conf|grep datadir|tr -s ' '|cut -d"=" -f 2`
mysql_data_base="$mysql_data_root/$database"
database_size=`du -k $mysql_data_base|tail -n 1|sed -r 's/\s+/\ /g'|cut -d" " -f 1`

# Expanded size (database + tar)
needed_size=$((database_size*3/2))
available_size=`df -k $BACKUP_PATH|tail -n 1|sed -r 's/\s+/\ /g'|cut -d" " -f 4`
available_size=$((available_size))

if [ $available_size -lt $needed_size ]
then
  if [ -n "$login" ]
  then
    info_script "Send a mail using $login login"
    # Name of the instance of mediboard
    instance=$(cd $BASH_PATH/../; pwd);
    instance=${instance##*/}
    wget "http://localhost/${instance}/?login=${login}&m=system&a=ajax_send_mail_diskfull"
  fi
  check_errs 2 "Needed space ($needed_size) exceeds available space ($available_size)"
fi

## Make MySQL medthod

# removes previous hotcopy/dump if something went wrong
rm -Rvf $database

DATETIME=$(date +%Y-%m-%dT%H-%M-%S)

case $1 in
  hotcopy)
    result=$database/
    
    mysqlhotcopy --quiet -u $username -p $password $database $BASE_PATH
    check_errs $? "Failed to create MySQL hot copy" "MySQL hot copy done!"
    
    if [ $binary_log -eq 1 ]; then
      databasebinlog=$database-${DATETIME}.binlog.position
      mysql --user=$username --password=$password $database < $BASH_PATH/mysql_show_master_status.sql > $BACKUP_PATH/binlog-${DATETIME}.index
      check_errs $? "Failed to create MySQL Binary log index" "MySQL Binary log index done!"
    fi
    ;;
  dump)
    result=$database.sql
    mysqldump --opt -u ${username} -p${password} $database > $database.sql
    check_errs $? "Failed to create MySQL dump" "MySQL dump done!"
    ;;
  *)
    result=$database/
    echo "Choose hotcopy or dump method"

    if [ -n "$lock" ]
    then
      rm $lock
    fi
    exit 1
    ;;
esac

# rotating files older than n days, all files if 0
if [ $time -eq 0 ]; then
  filter=""
else
  filter="-ctime +$time"
fi
find ${BASE_PATH} -name "$database*.tar.gz" $filter -exec /bin/rm '{}' ';'
check_errs $? "Failed to rotate files" "Files rotated"

# Compress archive and remove files

# Make the tarball
tarball=$database-${DATETIME}.tar.gz
tar cfz $tarball $result
check_errs $? "Failed to create backup tarball" "Tarball packaged!"

# Crypt the tarball
if [ -n "$passphrase" ]; then
  cat $tarball|openssl $cryptage -salt -out $tarball.aes -k $passphrase
  check_errs $? "Failed to crypt the archive" "Archive crypted!"
  # create a symlink
  cp -s -f $tarball.aes $database-latest.tar.gz.aes
  check_errs $? "Failed to create symlink of archive crypted" "Symlink of crypted archive created!"
  rm $tarball
  check_errs $? "Failed to delete non-crypted archive" "Archive non-crypted deleted!"
else
  # create a symlink
  cp -s -f $tarball $database-latest.tar.gz
  check_errs $? "Failed to create symlink" "Symlink created!"
fi



# Remove temporary files
rm -Rf $result
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"

if [ -n "$lock" ]
then
  rm $lock
fi

# Write event file
echo "#$(date +%Y-%m-%dT%H:%M:%S)" >> $event
echo "<strong>$database</strong> base backup: <strong>$method</strong> method" >> $event