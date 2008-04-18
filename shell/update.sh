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
  echo "  <action> The action to perform : show|do"
  echo "     show : Shows the update log"
  echo "     do   : Do the actual update"
  exit 1
fi
   
log=tmp/svnlog.txt
prefixes="erg|fnc|fct|bug|warn|edit|syst|svn"

case "$1" in
  show)
    svn info | awk 'NR==5'
    svn log -r BASE:HEAD | grep -i -E "(${prefixes})" | tac
    svn info -r HEAD | awk 'NR==5'
    ;;
    
  do)
    # Ensure log file exists
    touch $log;
    
    # Log file is revered, make it straight
    tac $log > $log.reversed
    mv -f $log.reversed $log
    
    # Concat the source (BASE) revision number : 5th line of SVN info (!)
    echo >> $log
    svn info | awk 'NR==5' >> $log

    # Concat SVN Log from BASE to HEAD
    svn log -r BASE:HEAD | grep -i -E "(${prefixes})" >> $log
    check_errs $? "Failed to append SVN log" "SVN log appended!"
    
    if [ "$1" = "do" ]
    then
      echo "+++ SHOULD UPDATE HERE +++" >> $log
    fi
    
    # Concat the target (HEAD) revision number
    echo >> $log
    svn info | awk 'NR==5' >> $log
    
    # Concat dating info
    echo "--- Updated Mediboard on $(date) ---" >> $log
    echo >> $log
    
    ## Reverse the log file for user convenience
    tac $log > $log.straight
    mv -f $log.straight $log
    ;;

  *)
    echo "Action $1 unknown" \
    ;; \
esac

