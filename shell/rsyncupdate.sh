#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
export LANG=fr_FR.utf-8

########
# Mediboard SVN updater and rsyncer
########

# Update
sh $BASH_PATH/update.sh $1 $2 $3

# Rsyncing -- Parsing rsyncupdate.conf

while read line
do
  first_character=`expr substr "$line" 1 1`
  # Skip comment lines and empty lines
  if [ "$first_character" != "#" ] && [ "$first_character" != "" ]
  then
    echo "-- Rsync $line --"
    rsync -apvzCP $BASH_PATH/.. --delete $line --exclude includes/config_overload.php --exclude tmp --exclude lib --exclude includes/config.php --exclude files --exclude images/pictures/logo_custom.png
    check_errs $? "Failed to rsync $line" "Succesfully rsync-ed $line"
    scp $BASH_PATH/../tmp/svnlog.txt $line/tmp/svnlog.txt
    scp $BASH_PATH/../tmp/svnstatus.txt $line/tmp/svnstatus.txt
  fi
done < $BASH_PATH/rsyncupdate.conf

exit 1