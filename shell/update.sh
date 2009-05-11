#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard SVN updater and user-oriented logger
########

announce_script "Mediboard SVN updater"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <action> [<revision>]"
  echo "  <action> The action to perform : info|real"
  echo "     info : Shows the update log"
  echo "     real : Performs the actual update"
  echo "  <revision> The revision number you want to update to, HEAD by default"
  exit 1
fi
   
log=$BASH_PATH/../tmp/svnlog.txt
tmp=$BASH_PATH/../tmp/svnlog.tmp
prefixes="erg|fnc|fct|bug|war|edi|sys|svn"
revision=HEAD

# Choose the target revision
if [ "$#" -eq 2 ]
then 
  revision=$2
fi

case "$1" in
  info)
    svn info | awk 'NR==5'
    svn log -r BASE:$revision | grep -i -E "(${prefixes})"
    svn info -r $revision | awk 'NR==5'
    ;;
    
  real)
    # Concat the source (BASE) revision number : 5th line of SVN info (!)
    svn info | awk 'NR==5' > $tmp
    check_errs $? "Failed to get source revision info" "SVN Revision source info written!"
    echo >> $tmp

    # Concat SVN Log from BASE to target revision
    svn log -r BASE:$revision | grep -i -E "(${prefixes})" >> $tmp
    check_errs $? "Failed to parse SVN log" "SVN log parsed!"
    
    # Perform actual update
    svn update --revision $revision
    check_errs $? "Failed to perform SVN update" "SVN updated performed!"

    # Concat the target revision number
    echo >> $tmp
    svn info | awk 'NR==5' >> $tmp
    check_errs $? "Failed to get target revision info" "SVN Revision target info written!"

    # Concat dating info
    echo "--- Updated Mediboard on $(date) ---" >> $tmp
    echo >> $tmp

    ## Concat tmp file to log file 

    # Ensure log file exists
    touch $log;
    
    # Log file is reversed, make it straight
    tac $log > $log.straight

    # Concat tmp file
    cat $tmp >> $log.straight
    
    # Reverse the log file for user convenience
    tac $log.straight > $log

    # Clean files
    rm -f $log.straight
    rm -f $tmp
    ;;

  *)
    echo "Action $1 unknown" \
    ;; \
esac

