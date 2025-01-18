/* Development config:
   ========================================================================== */

const webpack = require('webpack');
const {merge} = require('webpack-merge');
const baseWebpackConfig = require('./webpack.config');

const devWebpackConfig = merge(baseWebpackConfig, {
  mode: 'development',

  devServer: {
    historyApiFallback: true,
    contentBase: baseWebpackConfig.externals.paths.dist,
    port: 8081,
    overlay: {
      warnings: false,
      errors: true,
    },
  },
});

module.exports = new Promise((resolve, reject) => {
  resolve(devWebpackConfig);
});
