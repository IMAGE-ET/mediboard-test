#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Backups database on a daily basis
########

announce_script "Database daily backup"

if [ "$#" -lt 5 ]
then 
  echo "Usage: $0 <method> <username> <password> <database> <backup_path> \[<time>\]"
  echo "  <method> is hotcopy or dump method, eg hotcopy"
  echo "  <username> is username for mysql, eg admindb"
  echo "  <password> is password for mysql, eg dbadmin"
  echo "  <database> is database, eg mediboard"
  echo "  <backup_path> is the backup path, eg /var/backup"
  echo "  [<time>] is time of removal of files (day), default 7"
  exit 1
fi

method=$1
username=$2
password=$3
database=$4
backup_path=$5
if [ $6 ]
then
  time=$6
else
  time=7
fi

## Make complete path

# Make backup path
BACKUPPATH=$5
force_dir $BACKUPPATH

# Make database path
BASEPATH=${BACKUPPATH}/$database-db
force_dir $BASEPATH
cd ${BASEPATH}

## Make MySQL medthod

# removes previous hotcopy/dump if something went wrong
rm -Rvf $database

case $1 in
  hotcopy)
    result=$database/
    mysqlhotcopy -u $username -p $password $database $BASEPATH
    check_errs $? "Failed to create MySQL hot copy" "MySQL hot copy done!"
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

# deleting file whose date is greater than 7 days
find ${BASEPATH} -name "$database*.tar.gz" -ctime +$time -exec /bin/rm '{}' ';'
check_errs $? "Failed to delete files" "Files deleted"

## Compress archive and remove files
DATETIME=$(date +%Y-%m-%dT%H-%M-%S)

# Make the tarball
tarball=$database-${DATETIME}.tar.gz
tar cvfz $tarball $result
check_errs $? "Failed to create backup tarball" "Tarball packaged!"

# create a symlink
cp -s -f $tarball $database-latest.tar.gz
check_errs $? "Failed to create symlink" "Symlink created!"

# Remove temporary files
rm -Rvf $result
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"