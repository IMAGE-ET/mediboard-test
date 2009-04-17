#!/$HOME/sh

########
# Utilities
########

force_dir() 
{
  DIRPATH=$1
  if [ ! -d $DIRPATH ]
  then mkdir $DIRPATH
  fi
}

check_errs()
{
  RETURNCODE=$1
  FAILURETEXT=$2
  SUCCESSTEXT=$3

  if [ "${RETURNCODE}" -ne "0" ]
  then
    echo "ERROR # ${RETURNCODE} : ${FAILURETEXT}"
    # as a bonus, make our script exit with the right error code.
    echo "...Exiting..."
    exit ${RETURNCODE}
  fi

  echo ${SUCCESSTEXT}
}

announce_script()
{
  SCRIPTNAME=$1

  echo 
  echo "--- $SCRIPTNAME ($(date)) ---"
}

force_file()
{
  FILE=$1
  if [ ! -e $FILE ]
  then touch $FILE
  fi
}