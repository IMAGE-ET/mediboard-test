#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Send file by ftp

########
announce_script "Send file by ftp"

if [ "$#" -lt 4 ]
then 
  echo "Usage: $0 <host> <username> <password> <file> options"
  echo " <host> hostname of the ftp server"
  echo " <username> to access ftp"
  echo " <password> authenticate user"
  echo " <file> to send"
  echo " Options:"
  echo "   [-p <port>] the port to use, default 21"
  echo "   [-m ] source mode, default active"
  echo "   [-t ] transport mode, default binary"
  exit 1
fi

port=21
source_mode=''
transport_mode='binary'
args=`getopt p:mt $*`

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args

for i; do
  case "$i" in
    -p) port=$2; shift 2;;
    -m) source_mode='-p'; shift;;
    -t) transport_mode='ascii'; shift;;
    --) shift ; break ;;
  esac
done

host=$1
user=$2
password=$3
file=$4

ftp -n -v $source_mode $host $port <<END_SCRIPT
quote USER $user
quote PASS $password
$transport_mode
put $file
quit
END_SCRIPT
exit 0
