#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard SVN updater and user-oriented logger
########

announce_script "Mediboard SVN updater"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <action>"
  echo "  <action> The action to perform : info|real"
  echo "     info : Shows the update log"
  echo "     real   : Do the actual update"
  exit 1
fi
   
log=tmp/svnlog.txt
tmp=tmp/svnlog.tmp
prefixes="erg|fnc|fct|bug|war|edi|sys|svn"


case "$1" in
  info)
    svn info | awk 'NR==5'
    svn log -r BASE:HEAD | grep -i -E "(${prefixes})"
    svn info -r HEAD | awk 'NR==5'
    ;;
    
  real)
    # Concat the source (BASE) revision number : 5th line of SVN info (!)
    svn info | awk 'NR==5' > $tmp
    check_errs $? "Failed to get source revision info" "SVN Revision source info written!"
    echo >> $tmp

    # Concat SVN Log from BASE to HEAD
    svn log -r BASE:HEAD | grep -i -E "(${prefixes})" >> $tmp
    check_errs $? "Failed to parse SVN log" "SVN log parsed!"
    
    # Concat the target (HEAD) revision number
    echo >> $tmp
    svn info | awk 'NR==5' >> $tmp
    check_errs $? "Failed to get target revision info" "SVN Revision target info written!"

    # Concat dating info
    svn update
    check_errs $? "Failed to perform SVN update" "SVN updated performed!"

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

