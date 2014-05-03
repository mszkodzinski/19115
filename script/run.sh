#!/bin/bash

SELF=`readlink -f $0`
ROOT=`dirname $SELF`

cd $ROOT

valname=$(basename $ROOT)_INSTANCE_NAME

lock_filename=$(basename "$0")_$1_$2_lock
if [ ! -f $lock_filename ]; then

    touch $lock_filename
    /usr/bin/php $ROOT/../bootstrap/cli.php $1 $2
    rm $lock_filename

fi