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

if [[ $2 == "--skip-download" ]]
then 
  echo "BCB latest dump download skipped!"
else
	unlink $archive
	check_errs $? "Failed to remove former BCB dump archive" "Former BCB dump archive removed!"   
	
	wget http://mediboard:$password@www.mediboard.org/bcb/bcbdump_latest.zip \
	  --output-document $archive
	check_errs $? "Failed to download BCB dump" "BCBDump downloaded!"
fi

dump="$TMP_PATH/bcbdump_latest.sql"
unzip $archive -d $TMP_PATH
check_errs $? "Failed to extract BCB dump" "BCBDump extracted!"

config_path="$ROOT_PATH/includes/config.php"

find_property() 
{
	property_pattern=$(echo $1 | sed 's/ /.*/g')
	value_pattern=".*= [']\([a-z0-9]*\)['];"
	replacement="\1"
	$property_path
	grep "$property_pattern" $config_path | sed "s/$value_pattern/$replacement/"
}

current_dsn=$(find_property "dPmedicament CBcbObject dsn")
echo "Current BCB DSN detected is '$current_dsn'"

update_dsn=""
if [[ $current_dsn == "bcb1" ]]
then 
  update_dsn="bcb2"
fi

if [[ $current_dsn == "bcb2" ]]
then
  update_dsn="bcb1"
fi

if [[ -z $update_dsn ]]
then 
	echo "Current BCB DSN is neither 'bcb1' nor 'bcb2', 'bcb1' will be updated"
  update_dsn="bcb1"
fi

echo "BCB DSN to update is '$update_dsn'"

name=$(find_property "db $update_dsn dbname");
user=$(find_property "db $update_dsn dbuser");
pass=$(find_property "db $update_dsn dbpass");

mysql -u $user -p $pass $name -e "show tables" | grep -v Tables_in | grep -v "+" | \ gawk '{print "drop table " $1 ";"}' | mysql -u $user -p $pass $name
check_errs $? "Failed drop all tables in DSN '$update_dsn'" "All tables dropped in DSN '$update_dsn'"
