#!/bin/bash

for file in `find . -path ./vendor -prune -o -type f -name "*.php"`
do
    file -bi $file
    cp $file "$file.cp"
    iconv -f UTF8 -t CP1251 $file > "$file.cp"
    file -bi "$file.cp"
    cp "$file.cp" $file
    rm "$file.cp"
done

zip -r smartcat.connector.zip ./