#!/bin/bash

DIRECTORY_TO_COMPRESS="mz-mbo-plugin-temp"
ZIPPED_FILE="mz-mbo-plugin.zip"

cd ../
echo "Changing directory and making new temp dir."
mkdir mz-mbo-plugin-temp
cp -r mz-mindbody-api/lib mz-mbo-plugin-temp
cp -r mz-mindbody-api/inc mz-mbo-plugin-temp
cp -r mz-mindbody-api/dist mz-mbo-plugin-temp
cp mz-mindbody-api/*.php mz-mbo-plugin-temp
cp mz-mindbody-api/readme.txt mz-mbo-plugin-temp
echo "Files copied. Making zip file."
zip -r "$ZIPPED_FILE" "$DIRECTORY_TO_COMPRESS"
echo $DIRECTORY_TO_COMPRESS "compressed as" $ZIPPED_FILE
echo "Removing temp file and changing directories."
rm -r mz-mbo-plugin-temp
cd mz-mindbody-api
echo "Zip file made."