#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Configures groups and mods for Mediboard directories
########

announce_script "Mediboard directories groups and mods"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <apache_group> <sub_dir>"
  echo "  <apache_group> is the name of the primary group for Apache user"
  echo "  <sub_dir> [modules|style] (optional) is the sub-directory you want to apply changes on"
  exit 1
fi
   
APACHE_GROUP=$1

grep $APACHE_GROUP: /etc/group >/dev/null
if [ $? -ne "0" ]
then
  echo "Error: group '$APACHE_GROUP' does not exist"
  exit 1
fi

# Check optionnal sub-directory
SUB_DIR=$2
  if [ "$SUB_DIR" = "modules" ]
  then
    BASE_PATH="modules/*"
    SUB_PATH="modules/*/templates_c/"
  else
    if [ "$SUB_DIR" = "style" ]
    then
      BASE_PATH="style/*"
      SUB_PATH="style/*/templates_c/"
    else
      BASE_PATH="*"
      SUB_PATH="lib/ tmp/ files/ includes/ modules/*/locales/ modules/*/templates_c/ style/*/templates_c/ modules/hprimxml/xsd/ locales/*/"
    fi
  fi

# Change to Mediboard directory
MB_PATH=$(dirname $BASH_PATH)
cd $MB_PATH

# Change group to allow Appache to access files as group
echo $BASE
chgrp -R $APACHE_GROUP $BASE_PATH
check_errs $? "Failed to change files group to '$APACHE_GROUP'" "Files group changed to '$APACHE_GROUP'!"

# Remove write access to all files for group and other
chmod -R go-w $BASE_PATH
check_errs $? "Failed to protect all files from writing" "Files protected from writing!"

# Give write access to Apache for some directories
chmod -R g+w $SUB_PATH
check_errs $? "Failed to allow Apache writing to mandatory files" "Apache writing allowed for mandatory files!"
