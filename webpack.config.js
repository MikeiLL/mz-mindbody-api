const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const nodeExternals = require('webpack-node-externals');
const stylePath = './assets/styles';

module.exports = {
	context: path.resolve(__dirname, './assets'),
	target: 'node',
    externals: [nodeExternals()],
	entry: {
		main: './scripts/main.js',
		admin: './scripts/admin.js',
		colorbox: './scripts/colorbox.js',
		eventsDisplay: './scripts/events-display.js',
		filtertable: './scripts/jquery.filtertable.mz.js',
		scheduleDisplay: './scripts/schedule-display.js',
		signupModals: './scripts/signup-modals.js',
		staffPopup: './scripts/staff_popup.js'
	  },
  output: {
    path: path.resolve(__dirname, 'dist/scripts')
  },
  plugins: [new MiniCssExtractPlugin({
      publicPath: '../../'
    })],
  module: {
    rules: [
    	{
			test: /\.js$/,
			exclude: /node_modules/,
			use: {
			  loader: "babel-loader"
        }
      },
      {
        test: /\.(png|jpe?g|gif)$/i,
        use: [
          {
            loader: 'file-loader',
            options: {
              filename: './dist/images/[name].[ext]',
      		  context: './assets/images',
            },
          },
        ]
      },
      {
        test: /\.(scss)$/,
			use: [
				{
				loader: MiniCssExtractPlugin.loader,
				options: {
					filename: "./dist/styles/[name].css",
					context: path.resolve(__dirname, './assets/styles')
				}
			  },
			  {
				// Adds CSS to the DOM by injecting a `<style>` tag
				loader: 'style-loader'
			  },
			  {
				// Interprets `@import` and `url()` like `import/require()` and will resolve them
				loader: 'css-loader'
			  },
			  {
				// Loader for webpack to process CSS with PostCSS
				loader: 'postcss-loader',
				options: {
				  plugins: function () {
					return [
					  require('autoprefixer')
					];
				  }
				}
			  },
			  {
				// Loads a SASS/SCSS file and compiles it to CSS
				loader: 'sass-loader'
			  }
			]
      }
    ]
  }
};