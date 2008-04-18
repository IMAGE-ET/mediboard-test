#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard SVN updater and user-oriented logger
########

announce_script "Mediboard SVN updater"

log=tmp/svnlog.txt
prefixes="erg|fnc|fct|bug"

# Ensure log file exists
touch $log;

# Log file is revered, make it straight
tac $log > $log.reversed
mv -f $log.reversed $log

# Concat SVN Log from BASE to HEAD
svn log -r BASE:HEAD | grep -E "(${prefixes})" >> $log
check_errs $? "Failed to append SVN log" "SVN log appended!"

echo "+++ SHOULD UPDATE HERE +++" >> $log

# Concat the target (HEAD) revision number : 5th line of SVN info (!)
echo >> $log
svn info -r BASE| awk 'NR==5' >> $log

# Concat dating info
echo "--- Updated Mediboard on $(date) ---" >> $log
echo >> $log

## Reverse the log file for user convenience
tac $log > $log.straight
mv -f $log.straight $log

