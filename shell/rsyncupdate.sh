#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
export LANG=fr_FR.utf-8

########
# Mediboard SVN updater and rsyncer
########

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <action> [-r <revision> -c </path/to/another/config> -d]"
  echo "  <action> The action to perform : info|real|noup"
  echo "     info: Shows the update log, no rsync"
  echo "     real: Performs the actual update and the rsync"
  echo "     noup: No update, only rsync"
  echo "  -r <revision> The revision number you want to update to, HEAD by default"
  echo "  -c </path/to/another/config> Another config file to parse"
  echo "  -d Dry run : simulation of the rsync"
  exit 1
fi

conf_file=$BASH_PATH/rsyncupdate.conf
dry_run=""
revision=""

args=`getopt r:c:d $*`
if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args
for i; do
  case "$i" in
    -r) revision="-r $2"; shift 2;;
    -c) conf_file=$2; shift 2;;
    -d) dry_run="-n"; shift;;
    --) shift; break ;;
  esac
done

action=$1

# Update
if [ "$action" != "noup" ]
then
  echo "sh $BASH_PATH/update.sh $action $revision"
  sh $BASH_PATH/update.sh $action $revision
  check_errs $? "Wrong parameters" "Successfully updated"
fi

# File must exists (touch doesn't override)
touch $BASH_PATH/rsyncupdate.exclude

# Rsyncing -- Parsing rsyncupdate.conf
if [ "$action" != "info" ]
then

  while read line
  do
    first_character=`expr substr "$line" 1 1`
    # Skip comment lines and empty lines
    if [ "$first_character" != "#" ] && [ "$first_character" != "" ]
    then
      echo "Do you want to update $line (y or n) [default n] ? \c" ; read REPLY < /dev/tty
      if [ "$REPLY" = "y" ] ; then
        echo "-- Rsync $line --"
        eval rsync -avpgz $dry_run --stats $BASH_PATH/.. --delete $line --exclude-from=$BASH_PATH/rsyncupdate.exclude \
          --exclude includes/config_overload.php \
          --exclude /tmp \
          --exclude /lib \
          --exclude /files \
          --exclude includes/config.php \
          --exclude modules/hprimxml/xsd \
          --exclude images/pictures/logo_custom.png
        check_errs $? "Failed to rsync $line" "Succesfully rsync-ed $line"
        eval rsync -avzp $dry_run $BASH_PATH/../tmp/svnlog.txt $line/tmp/
        eval rsync -avzp $dry_run $BASH_PATH/../tmp/svnstatus.txt $line/tmp/
        eval rsync -avzp $dry_run $BASH_PATH/../tmp/monitevent.txt $line/tmp/
      fi
    fi
  done < $conf_file

fi
exit 1
