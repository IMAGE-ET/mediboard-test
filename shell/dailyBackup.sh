#!/bin/sh

BASH_PATH=$(dirname $BASH_SOURCE)
. $BASH_PATH/utils.sh

########
# Backups mediboard database on a daily basis
########

announce_script "Mediboard daily backup"

## Make complete path

# Make backup path
BACKUPPATH=/var/backup
force_dir $BACKUPPATH

# Make mediboard path
MEDIBOARDPATH=${BACKUPPATH}/mediboard-db
force_dir $MEDIBOARDPATH

# Make weekday path
WEEKDAY=$(date +%a)
WEEKDAYPATH=${MEDIBOARDPATH}/${WEEKDAY}
force_dir $WEEKDAYPATH
cd ${WEEKDAYPATH}

## Make MySQL safe copy

# removes previous hotcopy if something went wrong
rm -Rvf mediboard 
mysqlhotcopy -u mbadmin -p adminmb mediboard $WEEKDAYPATH
check_errs $? "Failed to create MySQL hot copy" "MySQL hot copy done!"

## Compress archive and remove files
DATETIME=$(date +%Y-%m-%dT%H-%M-%S)

# Make the tarball
rm -f mediboard*.tar.gz
tar cvfz mediboard-${DATETIME}.tar.gz mediboard/
check_errs $? "Failed to create backup tarball" "Tarball packaged!"

# Remove temporary files
rm -Rvf mediboard
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"


