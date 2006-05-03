#!/bin/sh

BASH_PATH=$(dirname $BASH_SOURCE)
. $BASH_PATH/utils.sh

########
# System and backups distant synchronisation
# Params :
#   remote location (ex: root@oxmytto.homelinux.com)
########

announce_script "Mediboard synchronisation"

# Backups directory
rsync -e ssh -avz $1:/var/backup /var/

# system directory
rsync -e ssh -avz $1:/var/www/html/mediboard /var/www/html/
