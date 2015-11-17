# MZ Mindbody API

Here's all the files to generate the plugin files for the mz mindbody api wordpress plugin.

The assets (javascript, fonts, images, css) are generated using Gulp and Bower.

### Install gulp and Bower

Generating the assets (in the dist directory) requires [node.js](http://nodejs.org/download/) . To install or update to the latest version of npm: `npm install -g npm@latest` .

Then, from the command line:

1.  Install [gulp](http://gulpjs.com) and [Bower](http://bower.io/) globally with `npm install -g gulp bower`
2.  From within the plugin directory, run `npm install`
3.  Run `bower install`

This should install all the necessary dependencies to run the build process: still within plugin directory, command line, enter the command `gulp`, and watch the magic (or the errors - don't be disheartened - it will all work out).
