#!/bin/sh

BASH_PATH=$(dirname $0)
ROOT_PATH="$BASH_PATH/../../.."
. $ROOT_PATH/shell/utils.sh
TMP_PATH="$ROOT_PATH/tmp"

########
# Mediboard BCB updater
########

announce_script "Mediboard BCB updater"

if [ "$#" -lt 1 ]
then 
  echo "Usage: $0 <password> [--skip-download]"
  echo "  <password> is the Mediboard portal password to access bcb/ folder"
  echo "  [--skip-download] to skip downloading the latest BCB dump"
  exit 1
fi

password=$1
archive="$TMP_PATH/bcbdump_latest.zip"
dump="$TMP_PATH/mysqldump.sql"

if [ "$2" = "--skip-download" ]
then 
  echo "BCB latest dump download skipped!"
else
  # Download latest BCB Dump
  rm -f $archive
  wget http://mediboard:$password@www.mediboard.org/bcb/bcbdump_latest.zip \
    --output-document $archive
  check_errs $? "Failed to download BCB dump" "BCBDump downloaded!"

  # Extract latest BCB dump
  dump="$TMP_PATH/mysqldump.sql"
  rm -f $dump
  unzip $archive -d $TMP_PATH
  check_errs $? "Failed to extract BCB dump" "BCBDump extracted!"
fi

config_path="$ROOT_PATH/includes/config.php"

###
# Retrieve a config in Mediboard config file
find_property() 
{
  property_pattern=$(echo $1 | sed "s/[a-zA-Z0-9]*/'&'/g" | sed "s/ /.*/g")
  value_pattern=".*= [']\([a-z0-9]*\)['];"
  replacement="\1"
  grep "$property_pattern" $config_path | sed "s/$value_pattern/$replacement/"
}

# Detect current DSN
current_dsn=$(find_property "dPmedicament CBcbObject dsn")
echo "Current BCB DSN detected is '$current_dsn'"

# Compute DSN to be updated
case "$current_dsn" in
  bcb1)
    update_dsn="bcb2"
    ;;
  bcb2)
    update_dsn="bcb1"
    ;;
  *)
    echo "Current BCB DSN is neither 'bcb1' nor 'bcb2'"
    update_dsn="bcb1"
    ;;
esac

echo "BCB DSN to update is '$update_dsn'"

# Retreive BCB data source connection params
name=$(find_property "db $update_dsn dbname");
user=$(find_property "db $update_dsn dbuser");
pass=$(find_property "db $update_dsn dbpass");
echo "BCB data source detail is: name '$name', user '$user', pass '$pass'"

# Empty the BCB data source

mysql -u $user -p$pass $name -e "show tables" \
 | grep -v Tables_in  \
 | grep -v "+" \
 | awk '{print "drop table " $1 ";"}' \
 | mysql -u $user -p$pass $name
check_errs $? "Failed to empty '$name' database" "Database '$name' emptied!"

# Fill in BDB dump
mysql -u $user -p$pass $name < $dump
check_errs $? "Failed to fill '$name' database with BCB dump" "BCB dump filled in '$name' database!"
