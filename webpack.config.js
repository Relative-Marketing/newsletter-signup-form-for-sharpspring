module.exports = {
	mode: 'production',
	entry: {
		app: './src/index.js'
	},
	output: {
		path: __dirname,
		filename: 'dist/index.js',
	},
	module: {
		rules: [{
			test: /.js$/,
			exclude: /node_modules/,
			use: [{
				loader: 'babel-loader'
			}]
		}]
	},
};