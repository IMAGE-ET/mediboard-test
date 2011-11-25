#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
export LANG=fr_FR.utf-8

########
# Mediboard SVN updater and rsyncer
########

# Update
sh $BASH_PATH/update.sh $1 $2 $3

# Rsyncing -- Parsing updatersync.conf

while read line
do
  first_character=`expr substr "$line" 1 1`
  # Skip comment lines and empty lines
  if [ "$first_character" != "#" ] && [ "$first_character" != "" ]
  then
    echo "-- Rsync $line --"
    rsync -avzCP $BASH_PATH/.. $line --exclude includes/config_overload.php --exclude tmp --exclude lib
    check_errs $? "Failed to rsync $line" "Succesfully rsync-ed $line"
  fi
done < $BASH_PATH/updatersync.conf

exit 1