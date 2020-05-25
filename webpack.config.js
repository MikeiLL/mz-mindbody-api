const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const nodeExternals = require('webpack-node-externals');
const stylePath = './assets/styles';

module.exports = {
	context: path.resolve(__dirname, './assets'),
	target: 'node',
    externals: [nodeExternals()],
	entry: {
		main: ['./scripts/main.js', './styles/main.scss'],
		admin: './scripts/admin.js',
		colorbox: './scripts/colorbox.js',
		'events-display': './scripts/events-display.js',
		'jquery.filtertable.mz': './scripts/jquery.filtertable.mz.js',
		'schedule-display': './scripts/schedule-display.js',
		'signup-modals': './scripts/signup-modals.js',
		'staff_popup': './scripts/staff_popup.js',
		'admin-style': './styles/admin-style.scss',
		'loading': './images/loading.gif',
		'border': './images/border.png',
		'controls': './images/controls.png',
		'loading_background': './images/loading_background.png',
		'overlay': './images/overlay.png'
	  },
  output: {
    path: path.resolve(__dirname, 'dist')
  },
  plugins: [new MiniCssExtractPlugin({
      publicPath: '../../',
      filename: "./styles/[name].css"
    })],
  module: {
    rules: [
    	{
			test: /\.js$/,
			exclude: /node_modules/,
			use: {
			  loader: "babel-loader",
			  options: {

				},
        }
      },
      {
        test: /\.(png|jpe?g|gif)$/i,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'images/',
              name: '[name].[ext]'
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
					
				}
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