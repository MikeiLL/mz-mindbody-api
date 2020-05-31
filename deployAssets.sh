#! /bin/bash
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.

# main config
PLUGINSLUG="mz-mindbody-api"
CURRENTDIR=`pwd`
MAINFILE="mz-mindbody.php" # this should be the name of your main php file in the wordpress plugin

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/mz-mindbody-api" # Remote SVN repo on wordpress.org, with no trailing slash
SVNUSER="mikeill" # your svn username


# Let's begin...
echo ".........................................."
echo 
echo "Preparing to update plugin assets"
echo 
echo ".........................................."
echo 

echo 
echo "Create local copy of SVN repo ..."
svn co $SVNURL $SVNPATH

echo "Clear svn assets so we can overwrite them"
rm -rf $SVNPATH/assets/*

echo "Ignore github specific files and deployment script"
svn propset svn:ignore "
README.md
node_modules
tests
.DS_Store
.gitmodules
assets
package.json
bin/install-wp-tests.sh
phpunit.xml.dist
phpcs.ruleset.xml
phpcs.xml.dist
.git
*.log
*.sh
wpassets
.gitignore" "$SVNPATH/trunk/"

echo -e "Enter a commit message for new assets: \c"
read COMMITMSG

echo "Copy assets to SVN repo "
cp -r wpassets/ $SVNPATH/assets/

echo "Move to temp SVN repo location and add assets"
cd $SVNPATH
# May have to do this manually:
# svn add assets

svn commit --username=$SVNUSER -m "$COMMITMSG"

echo "Return to plugin directory"
cd $CURRENTDIR

echo "Remove temporary directory $SVNPATH"
rm -fr $SVNPATH/

echo "*** Done. ***"
