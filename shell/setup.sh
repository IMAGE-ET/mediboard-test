#!/bin/sh

BASH_PATH=$(dirname $BASH_SOURCE)
. $BASH_PATH/utils.sh

########
# Configures groups and mods for Mediboard directories
########

announce_script "Mediboard directories groups and mods"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <apache_group>"
  echo "  <apache_group> is the name of the primary group for Apache user"
  exit 1
fi
   
APACHE_GROUP=$1

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
      SUB_PATH="lib/ tmp/ files/ includes/ modules/*/templates_c/ style/*/templates_c/ modules/dPinterop/hprim/"
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