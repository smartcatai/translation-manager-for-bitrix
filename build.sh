#!/bin/bash

DIR=`dirname "$0"`

rm -rf $DIR/.last_version
rm $DIR/smartcat.connector.zip

for dirs in `find * -path $DIR -o -type d -print`
do
    mkdir -p $DIR/.last_version/$dirs
done

for fileb in `find * -path $DIR -o -type f -print`
do
    cp $DIR/$fileb $DIR/.last_version/$fileb
done

for file in `find * -path vendor -prune -o -type f -name "*.php" -print`
do
    iconv -f UTF8 -t CP1251 $DIR/$file > $DIR/.last_version/$file
done

rm $DIR/.last_version/build.sh $DIR/.last_version/composer.*
zip -r smartcat.connector.zip $DIR/.last_version