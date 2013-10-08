#!/bin/sh
username=$1
password=$2
host="localhost"
instance="mediboard"

args=$(getopt h:i: $*)

if [ $? != 0 ] ; then
  echo "Invalid argument. Check your command line"; exit 0;
fi

set -- $args
for i; do
  case "$i" in
    -h) host=$2; shift 2;;
    -i) instance=$2; shift 2;;
    --) shift ; break ;;
  esac
done

auth="login=1&username=${username}&password=${password}"
log="/var/log/mediboard/echanges.purge.log"
doc="/var/log/mediboard/echanges.purge.html"

moda="m=webservices&a=ajax_purge_echange&do_purge=1&months=1&max=10000&delete=1"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

moda="m=ftp&a=ajax_purge_echange&do_purge=1&months=1&max=10000&delete=1"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

moda="m=hprimxml&a=ajax_purge_echange&do_purge=1&months=1&max=10000"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"

moda="m=hl7&a=ajax_purge_exchange&do_purge=1&months=1&max=10000"
wget -a ${log} -O ${doc} "http://${host}/${instance}/?${auth}&${moda}"