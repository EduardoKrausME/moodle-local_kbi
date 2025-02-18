#!/usr/bin/env bash

rm -f ./../../db/install/*.min.js

mv *.min.js              ../build/

find ../../ -name "*.min.min.js" -depth -exec rm -v {} \;
find ../../ -name ".DS_Store"    -depth -exec rm -v {} \;

