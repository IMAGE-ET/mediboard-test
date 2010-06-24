#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
export LANG=fr_FR.utf-8

########
# Mediboard SVN updater and user-oriented logger
########

announce_script "Mediboard SVN updater"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <action> [-r <revision>]"
  echo "  <action> The action to perform : info|real"
  echo "     info : Shows the update log"
  echo "     real : Performs the actual update"
  echo "  -r <revision> The revision number you want to update to, HEAD by default"
  exit 1
fi
   
MB_PATH=$BASH_PATH/..
log=$MB_PATH/tmp/svnlog.txt
tmp=$MB_PATH/tmp/svnlog.tmp
dif=$MB_PATH/tmp/svnlog.dif
status=$MB_PATH/tmp/svnstatus.txt
prefixes="erg|fnc|fct|bug|war|edi|sys|svn"
revision=HEAD

# Choose the target revision

args=`getopt r: $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args

for i; do
  case "$i" in
    -r) revision=$2; shift 2;;
    --) shift ; break ;;
    -*) echo "$0: error - unrecognized option $1" 1>&2; exit 1;;
  esac
done

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
    svn log $MB_PATH -r BASE:$revision > $dif
    check_errs $? "Failed to retrieve SVN log" "SVN log retrieved!"
    
    grep -i -E "(${prefixes})" $dif >> $tmp
    echo "SVN log parsed!"
    # Don't check beacause grep returns 1 if no occurence found
    rm -f $dif
    
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

    # Write status file
    svn info | awk 'NR==5' > $status
    echo "Date: $(date +%Y-%m-%dT%H:%M:%S)" >> $status
    check_errs $? "Failed to write status file" "Status file written!"
    ;;

  *)
    echo "Action $1 unknown" \
    ;; \
esac