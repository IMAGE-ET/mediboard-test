#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Backups AMMAX database on a daily basis
########

announce_script "AMMAX daily backup"

## Make complete path

# Make backup path
BACKUPPATH=/var/backup
force_dir $BACKUPPATH

# Make ammax path
AMMAXPATH=${BACKUPPATH}/AMMAX
force_dir $AMMAXPATH

# Make weekday path
WEEKDAY=$(date +%a)
WEEKDAYPATH=${AMMAXPATH}/${WEEKDAY}
force_dir $WEEKDAYPATH
cd ${WEEKDAYPATH}

## Make MySQL safe copy

# removes previous hotcopy if something went wrong
rm -Rvf AMMAX 
mysqlhotcopy -u mbadmin -p adminmb AMMAX $WEEKDAYPATH
check_errs $? "Failed to create MySQL hot copy" "MySQL hot copy done!"

## Compress archive and remove files
DATETIME=$(date +%Y-%m-%dT%H-%M-%S)

# Make the tarball
rm -f AMMAX*.tar.gz
tar cvfz AMMAX-${DATETIME}.tar.gz AMMAX/
check_errs $? "Failed to create backup tarball" "Tarball packaged!"

# Remove temporary files
rm -Rvf AMMAX
check_errs $? "Failed to clean MySQL files" "MySQL files cleansing done!"


