#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh
MB_PATH=$(cd $BASH_PATH/../; pwd);

########
# Miner launcher
########

announce_script "Miner launcher"

if [ "$#" -lt 3 ]
then
  echo "Usage: $0 <instance> <username> <password> Options"
  echo " <instance> instance name"
  echo " <username> to access database"
  echo " <password> authenticate user"
  echo " Options:"
  echo "   [-f <file>] output file"
  exit 1
fi

args=$(getopt f: $*)

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

file=''

set -- $args

for i; do
  case "$i" in
    -f) file="-f $2"; shift 2;;
    --) shift ; break ;;
  esac
done

instance=$1
username=$2
password=$3

info_script "Miner launcher"

list=$(ls -C1 $MB_PATH/modules/*/datamine.php|cut -d'/' -f 7)

for i in $list
do
  report=$(sh $MB_PATH/shell/request.sh "http://localhost/$instance" $username $password "m=$i&a=datamine&suppressHeaders=1&dialog=1" $file);
done
