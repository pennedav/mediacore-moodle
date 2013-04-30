#!/bin/bash

set -e

usage() {
	printf "USAGE:\n\
-h (prints this help)\n\
-s (source: MediaCore Moodle repo dir)\n\
-d (destination: Moodle v2.4+ install dir)\n\
DEPENDENCIES:\n\
bindfs (https://code.google.com/p/bindfs/)\n"
}

SOURCE=
DEST=

while getopts ":hs:d:" opt; do
	case $opt in
		h)
			usage
			exit 1
			;;
		s)
			SOURCE=$OPTARG
			;;
		d)
			DEST=$OPTARG
			;;
		\?)
			printf "Invalid option: -$OPTARG" >&2
			exit 1
			;;
		:)
			printf "Option -$OPTARG requires an argument." >&2
			exit 1
	esac
done

if [[ -z "$SOURCE" || -z "$DEST" ]]; then
	usage
	exit 1
fi

if [[ "$SOURCE" == "." || "$DEST" == "." ]]; then
	printf "You probably didn't mean to use '.' as a path!\n"
	exit 1
fi

if [ ! -d $SOURCE ]; then
	printf "SOURCE DIR NOT FOUND:\n\
MediaCore Moodle repository directory '$SOURCE' not found.\n"
	exit 1
fi

if [ ! -d $DEST ]; then
	printf "DESTINATION DIR NOT FOUND:\n\
Moodle v2.4+ install directory '$DEST' not found.\n"
	exit 1
fi

currdir=${PWD}
cd $DEST;
echo "Mounting MediaCore fs volumes in '$DEST'...";
set -x
mkdir -p filter/mediacore;
mkdir -p lib/editor/tinymce/plugins/mediacore;
mkdir -p local/mediacore;
mkdir -p repository/mediacore;
bindfs $SOURCE/filter/mediacore filter/mediacore;
bindfs $SOURCE/lib/editor/tinymce/plugins/mediacore lib/editor/tinymce/plugins/mediacore;
bindfs $SOURCE/local/mediacore local/mediacore;
bindfs $SOURCE/repository/mediacore repository/mediacore;
cd $currdir;
