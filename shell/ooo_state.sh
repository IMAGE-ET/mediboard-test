#!/bin/sh
darwin_kernel=`uname -a|cut -d' ' -f1`

# Pour mac
if [ $darwin_kernel = "Darwin" ]
then
  APACHE_USER=`ps -ef|grep httpd|head -2|tail -1|cut -d' ' -f4`

# Distributions linux
else
  APACHE_USER=`ps -ef|grep apache|head -2|tail -1|cut -d' ' -f1`
fi

# R?cup?ration du pid de soffice
res=`ps -u $APACHE_USER|grep soffice`


space=`echo ${res:0:1}|cut -c 1`

# Si le pid du processus soffice commence par un espace, on le supprime
if [ "$space" == " " ]
then
  res=`echo $res|awk -F"^ +| +$" '{print $2"."}'|cut -d' ' -f1`
fi

if [ "$res" = '' ]
then
  echo 0
  echo $res
  echo $APACHE_USER
else
  echo 1
  echo $res
  echo $APACHE_USER
fi