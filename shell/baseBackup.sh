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
  echo "   [-m <mail>] mail address to send if diskfull detected"
  exit 1
fi

mail=''
time=7
binary_log=0
args=`getopt m:t:b $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args

for i; do
  case "$i" in
    -t) time=$2; shift 2;;
    -m) mail=$2; shift 2;;
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

## If no enough free disk space (1.5 * size of database), send mail if provided and quit
conf=`find /etc -name my.cnf 2>/dev/null|head -n 1`

dir_mysql=`cat $conf|grep datadir|tr -s ' '|cut -d"=" -f 2`
dir_mysql="$dir_mysql/$database"
size_database=`du -k $dir_mysql|tail -n 1|sed -r 's/\s+/\ /g'|cut -d" " -f 1`

# Expanded size (database + tar)
size_expanded=`echo "$size_database*1.5"|bc -l`
freedisk=`df -k $BACKUP_PATH|tail -n 1|sed -r 's/\s+/\ /g'|cut -d" " -f 4`

# The size expanded is float, so execute the test with bc command
full=`echo "$freedisk < $size_expanded"|bc`

if [ $full -eq 1 ]
then
  if [ ${#mail} -gt 1 ]
  then
    # Name of the instance of mediboard
    instance=$(cd $BASH_PATH/../; pwd);
    instance=${instance##*/}
    wget "http://localhost/${instance}/index.php?login=1&username=cron&password=alltheway&m=system&a=ajax_send_mail_diskfull&mail=${mail}"
  fi
  exit 0
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

# create a symlink
cp -s -f $tarball $database-latest.tar.gz
check_errs $? "Failed to create symlink" "Symlink created!"

# Remove temporary files
rm -Rf $result
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"
