#! /bin/bash
# A modification of Dean Clatworthy's deploy script as found here: https://github.com/deanc/wordpress-plugin-git-svn
# The difference is that this script lives in the plugin's git repo & doesn't require an existing SVN repo.

# main config
PLUGINSLUG="mz-mindbody-api"
CURRENTDIR=`pwd`
MAINFILE="mZ-mindbody-api.php" # this should be the name of your main php file in the wordpress plugin

# git config
GITPATH="$CURRENTDIR/" # this file should be in the base of your git repository

# svn config
SVNPATH="/tmp/$PLUGINSLUG" # path to a temp SVN repo. No trailing slash required and don't add trunk.
SVNURL="http://plugins.svn.wordpress.org/mz-mindbody-api" # Remote SVN repo on wordpress.org, with no trailing slash
SVNUSER="mikeill" # your svn username


# Let's begin...
echo ".........................................."
echo 
echo "Preparing to deploy wordpress plugin"
echo 
echo ".........................................."
echo 

cd $GITPATH

echo "Creating local copy of SVN repo ..."
svn co $SVNURL $SVNPATH

echo "Clearing svn repo so we can overwrite it"
svn rm $SVNPATH/trunk/*

echo "Exporting the HEAD of master from git to the trunk of SVN"
git checkout-index -a -f --prefix=$SVNPATH/trunk/

echo "Ignoring github specific files and deployment script"
svn propset svn:ignore "deploy.sh
README.md
bower_components
node_modules
.DS_Store
.gitmodules
advanced
assets
gulpfile.js
bower.json
package.json
.git
.gitignore" "$SVNPATH/trunk/"

echo "Changing directory to SVN and committing to trunk"
cd $SVNPATH/trunk/
# Add all new files that are not set to be ignored
#svn status | grep -v "^.[ \t]*\..*" | grep "^?" | awk '{print $2}' | xargs svn add
#svn commit --username=$SVNUSER -m "$COMMITMSG"

#echo "Creating new SVN tag & committing it"
#cd $SVNPATH
#svn copy trunk/ tags/$NEWVERSION1/
#cd $SVNPATH/tags/$NEWVERSION1
#svn commit --username=$SVNUSER -m "Tagging version $NEWVERSION1"

#echo "Removing temporary directory $SVNPATH"
#rm -fr $SVNPATH/

echo "*** FIN *** at $SVNPATH"
