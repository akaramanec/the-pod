/* Build config:
   ========================================================================== */
const {merge} = require('webpack-merge');
const baseWebpackConfig = require('./webpack.config');
const ImageMinPlugin = require('imagemin-webpack-plugin').default;
const imageMinMozjpeg = require('imagemin-mozjpeg');
const imageMinPngquant = require('imagemin-pngquant');



const buildWebpackConfig = merge(baseWebpackConfig, {
  plugins: [
    // new ImageMinPlugin({
    //
    //   test: /\.(jpe?g|png|gif|svg)$/i,
    //   plugins: [
    //     imageMinMozjpeg({
    //       progressive: false,
    //       quality: 65,
    //     }),
    //     imageMinPngquant({
    //       quality: [0.65, 0.90],
    //       speed: 4,
    //     }),
    //   ],
    // }),
  ],
});

module.exports = new Promise((resolve, reject) => {
  resolve(buildWebpackConfig);
});
