#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Backups mediboard database on a daily basis
########

announce_script "Mediboard daily backup"

sh $BASH_PATH/baseBackup.sh hotcopy mbadmin adminmb mediboard /var/backup