#!/bin/sh
username=$1
password=$2

auth="login=1&username=${username}&password=${password}"
moda="m=webservices&a=ajax_purge_echange&do_purge=1&months=1&max=10000"
log="/var/log/mediboard/echanges.purge.log"
doc="/var/log/mediboard/echanges.purge.html"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"

moda="m=hprimxml&a=ajax_purge_echange&do_purge=1&months=1&max=10000"
log="/var/log/mediboard/echanges.purge.log"
doc="/var/log/mediboard/echanges.purge.html"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"

moda="m=ftp&a=ajax_purge_echange&do_purge=1&months=1&max=10000"
log="/var/log/mediboard/echanges.purge.log"
doc="/var/log/mediboard/echanges.purge.html"
wget -a ${log} -O ${doc} "http://localhost/mediboard/?${auth}&${moda}"