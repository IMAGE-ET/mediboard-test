#!/bin/sh
username=$1
password=$2

auth="login=1&username=${username}&password=${password}"
log="/var/log/mediboard/echanges.purge.log"
doc="/var/log/mediboard/echanges.purge.html"

moda="m=webservices&a=ajax_purge_echange&do_purge=1&months=1&max=10000&delete=1"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"

moda="m=ftp&a=ajax_purge_echange&do_purge=1&months=1&max=10000&delete=1"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"

moda="m=hprimxml&a=ajax_purge_echange&do_purge=1&months=1&max=10000"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"

moda="m=hl7&a=ajax_purge_exchange&do_purge=1&months=1&max=10000"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"