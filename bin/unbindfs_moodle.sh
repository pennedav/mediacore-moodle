#!/bin/bash

set -e

usage() {
	printf "USAGE:\n\
unbindfs_moodle.sh [moodle v2.4+ root directory]\n"
	exit 1
}

if [ $# -eq 0 ]; then
	echo "No arguments supplied"
	usage
	exit 1
fi

if [ ! -d $1 ]; then
	printf "NOT FOUND:\n\
Moodle v2.4+ root directory '$1' not found.\n"
	exit 1
fi

currdir=${PWD}
cd $1;
echo "Unmounting fs volumes from '$1'...";
set -x
umount -v filter/mediacore;
umount -v lib/editor/tinymce/plugins/mediacore;
umount -v local/mediacore;
umount -v repository/mediacore;
rm -rf filter/mediacore;
rm -rf lib/editor/tinymce/plugins/mediacore;
rm -rf local/mediacore;
rm -rf repository/mediacore;
cd $currdir;
