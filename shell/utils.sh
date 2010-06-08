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

package_lib()
{
  # $1 : library name
  # $2 : Url
  # $3 : Version
  
  echo "Retrieve dompdf from $2";
  svn co $2 tmp/$1;
  tar cfz tmp/$1-$3.tar.gz --directory ./tmp/ $1 --exclude=.svn;
  check_errs $? "Failed to package $1" "$1 packaged!";
  mv ./tmp/$1-$3.tar.gz libpkg/;
}