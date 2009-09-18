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
   
MB_PATH=$BASH_PATH/..
log=$MB_PATH/tmp/svnlog.txt
tmp=$MB_PATH/tmp/svnlog.tmp
status=$MB_PATH/tmp/svnstatus.txt
prefixes="erg|fnc|fct|bug|war|edi|sys|svn"
revision=HEAD

# Choose the target revision
if [ "$#" -eq 2 ]
then 
  revision=$2
fi

case "$1" in
  info)
    svn info $MB_PATH | awk 'NR==5'
    svn log  $MB_PATH -r BASE:$revision | grep -i -E "(${prefixes})"
    svn info $MB_PATH -r $revision | awk 'NR==5'
    ;;
    
  real)
    # Concat the source (BASE) revision number : 5th line of SVN info (!)
    svn info $MB_PATH | awk 'NR==5' > $tmp
    check_errs $? "Failed to get source revision info" "SVN Revision source info written!"
    echo >> $tmp

    # Concat SVN Log from BASE to target revision
    svn log $MB_PATH -r BASE:$revision | grep -i -E "(${prefixes})" >> $tmp
    check_errs $? "Failed to parse SVN log" "SVN log parsed!"
    
    # Perform actual update
    svn update $MB_PATH --revision $revision
    check_errs $? "Failed to perform SVN update" "SVN updated performed!"

    # Concat the target revision number
    echo >> $tmp
    svn info $MB_PATH | awk 'NR==5' >> $tmp
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

    # Write status file
    svn info | awk 'NR==5' > $status
    echo "Date: $(date)" >> $status
  *)
    echo "Action $1 unknown" \
    ;; \
esac