#!/bin/sh

BASH_PATH=$(dirname $BASH_SOURCE)
. $BASH_PATH/utils.sh

########
# Configures groups and mods for Mediboard directories
########

announce_script "Mediboard directories groups and mods"

# Check Apache group paramater
APACHE_GROUP=$1
  if [ -z "$APACHE_GROUP" ]
  then
    echo "Error, usage is: setup.sh apache_group"
    exit 0
  fi

# Change to Mediboard directory
MB_PATH=$(dirname $BASH_PATH)
cd $MB_PATH

# Change group to allow Appache to access files as group
chgrp -R $APACHE_GROUP *
check_errs $? "Failed to change files group to '$APACHE_GROUP'" "Files group changed to '$APACHE_GROUP'!"

# Remove write access to all files for group and other
chmod -R go-w *
check_errs $? "Failed to protect all files from writing" "Files protected from writing!"

# Give write access to Apache for some directories
chmod -R g+w lib/ tmp/ files/ includes/ modules/*/templates_c/
check_errs $? "Failed to allow Apache writing to mandatory files" "Apache writing allowed for mandatory files!"



