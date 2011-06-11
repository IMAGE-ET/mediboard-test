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
  exit 1
fi

time=7
binary_log=0
args=`getopt t:b $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args

for i; do
  case "$i" in
    -t) time=$2; shift 2;;
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

# Make backup path
BACKUP_PATH=$5
force_dir $BACKUP_PATH

# Make database path
BASE_PATH=${BACKUP_PATH}/$database-db
force_dir $BASE_PATH
cd ${BASE_PATH}

## Make MySQL medthod

# removes previous hotcopy/dump if something went wrong
rm -Rvf $database

case $1 in
  hotcopy)
    result=$database/
    
    mysqlhotcopy --quiet -u $username -p $password $database $BASE_PATH
    check_errs $? "Failed to create MySQL hot copy" "MySQL hot copy done!"
    
    if [ $binary_log -eq 1 ]; then
      databasebinlog=$database-${DATETIME}.binlog.position
      mysql --user=$username --password=$password $database < $BASH_PATH/mysql_show_master_status.sql > $BACKUP_PATH/binlog.index
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
    exit 1
    ;;
esac

# deleting file whose date is greater than n days, all files if 0
if [ $time -eq 0 ]; then
  filter=""
else
  filter="-ctime +$time"
fi
find ${BASE_PATH} -name "$database*.tar.gz" $filter -exec /bin/rm '{}' ';'
check_errs $? "Failed to delete files" "Files deleted"

# Compress archive and remove files
DATETIME=$(date +%Y-%m-%dT%H-%M-%S)

# Make the tarball
tarball=$database-${DATETIME}.tar.gz
tar cfz $tarball $result
check_errs $? "Failed to create backup tarball" "Tarball packaged!"

# create a symlink
cp -s -f $tarball $database-latest.tar.gz
check_errs $? "Failed to create symlink" "Symlink created!"

# Remove temporary files
rm -Rf $result
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"
