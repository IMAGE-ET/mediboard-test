#!/bin/sh

BASH_PATH=$(dirname $0)
. $BASH_PATH/utils.sh

########
# Mediboard arial.ttf installer
########

announce_script "Mediboard arial.ttf installer"

force_dir /usr
force_dir /usr/X11R6
force_dir /usr/X11R6/lib
force_dir /usr/X11R6/lib/X11
force_dir /usr/X11R6/lib/X11/fonts
force_dir /usr/X11R6/lib/X11/fonts/truetype

cp $BASH_PATH/arial.ttf /usr/X11R6/lib/X11/fonts/truetype
check_errs $? "Failed to copy arial font" "Arial font copied!"
