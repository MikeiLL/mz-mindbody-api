#!/bin/bash

DIRECTORY_TO_COMPRESS="mz-mindbody-api"
ZIPPED_FILE="mz-mbo-plugin.zip"

cd ../
echo "Changing directory and moving to new temp dir."
mv mz-mindbody-api mz-mindbody-api-temp
echo "Make temporary version of the plugin and copying desired files."
mkdir mz-mindbody-api
cp -r mz-mindbody-api-temp/lib mz-mindbody-api
cp -r mz-mindbody-api-temp/inc mz-mindbody-api
cp -r mz-mindbody-api-temp/dist mz-mindbody-api       
cp -r mz-mindbody-api-temp/mindbody-php-api mz-mindbody-api
cp mz-mindbody-api-temp/*.php mz-mindbody-api
cp mz-mindbody-api-temp/readme.txt mz-mindbody-api
echo "Files copied. Making zip file."
zip -r "$ZIPPED_FILE" "$DIRECTORY_TO_COMPRESS"
echo $DIRECTORY_TO_COMPRESS "compressed as" $ZIPPED_FILE > /dev/null
echo "Removing temp file and changing directories."
rm -r mz-mindbody-api
echo "Renaming to original directory name."
mv mz-mindbody-api-temp mz-mindbody-api
cd mz-mindbody-api
echo "Zip file made and back home again."