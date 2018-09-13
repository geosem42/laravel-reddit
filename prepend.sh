#!/bin/sh
#add Line at the top of File
# @author Abdennour TOUMI
if [ -e $2 ]; then
    sed -i -e '1i$1\' $2
fi
