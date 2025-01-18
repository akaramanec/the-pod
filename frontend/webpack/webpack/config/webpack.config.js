/* Base config:
   ========================================================================== */
const webpack = require('webpack');
const path = require('path');
const fs = require('fs');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const {VueLoaderPlugin} = require('vue-loader');
const {CleanWebpackPlugin} = require('clean-webpack-plugin');
const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
// Main const. Feel free to change it
const isDev = process.env.NODE_ENV === 'development';
const isProd = !isDev;
const PATHS = {
  src: path.join(__dirname, '../../src'),
  dist: path.join(__dirname, (isProd ? '../../public' : '../../dist')),
  webpack: path.join(__dirname, '../../webpack'),
  assets: 'assets/',
};

const fileName = ext => isDev ? `[name].${ext}` : `[name].${ext}`
const plugins = (type) => {
  const base = [
    new VueLoaderPlugin(),
    new MiniCssExtractPlugin({
      filename: isProd ? `../../web/assets/css/${fileName('css')}` : `${PATHS.assets}css/${fileName('css')}`,
    }),
    new webpack.ProvidePlugin({
      '$': 'jquery',
      'jQuery': 'jquery',
      'window.jQuery': 'jquery',
    }),
    new CopyWebpackPlugin({
      patterns: [
        {from: `${PATHS.src}/${PATHS.assets}img`, to: `${PATHS.assets}img`},
        {from: `${PATHS.src}/${PATHS.assets}fonts`, to: `${PATHS.assets}fonts`},
        {from: `${PATHS.src}/pages/php`, to: ``},
        {from: `${PATHS.src}/static`, to: ''},
      ]
    }),
    new CleanWebpackPlugin(),
  ];
  switch (type) {
    case 'html': {
      const PAGES_DIR = `${PATHS.src}/pages`;
      const PAGES = fs
        .readdirSync(PAGES_DIR)
        .filter((fileName) => fileName.endsWith('.html'));
      base.push(
        ...PAGES.map(
          (page) =>
            new HtmlWebpackPlugin({
              template: `${PAGES_DIR}/${page}`,
              filename: page,
            }),
        ),);
      break;
    }

    case 'pug': {
      const PAGES_DIR = `${PATHS.src}/pages/pug/includes/pages`;
      const PAGES = fs
        .readdirSync(PAGES_DIR)
        .filter((fileName) => fileName.endsWith('.pug'));
      base.push(
        ...PAGES.map(
          (page) =>
            new HtmlWebpackPlugin({
              template: `${PAGES_DIR}/${page}`,
              filename: (page === 'index.pug' || page === '404.pug' ?
                page.replace(/\.pug/, '.html') :
                `${page.split('.')[0]}/index.html`),
            }),
        ),);
      break;
    }
  }
  const PAGES_PHP = fs
    .readdirSync(PATHS.src)
    .filter((fileName) => fileName.endsWith('.php'));
  base.push(
    ...PAGES_PHP.map(
      (page) =>
        new HtmlWebpackPlugin({
          template: `${PATHS.src}/${page}`,
          filename: `${page}`,
          inject: false,
        }),
    ),)
  return base
}


module.exports = {
  externals: {
    paths: PATHS,
  },
  entry: {
    app: ['@babel/polyfill', PATHS.webpack]
    // module: `${PATHS.src}/your-module.js`,
  },
  output: {
    filename: `${PATHS.assets}js/${fileName('js')}`,
    path: PATHS.dist,
    publicPath: isDev ? '/' : '/',

  },
  optimization: {
    splitChunks: {
      cacheGroups: {
        vendor: {
          name: 'vendors',
          test: /node_modules/,
          chunks: 'all',
          enforce: true,
        },
        // css: {
        //   name: 'css',
        //   test: /\.css$/,
        //   chunks: 'all',
        //   enforce: true,
        // },
        // scss: {
        //   name: 'scss',
        //   test: /\.scss$/,
        //   chunks: 'all',
        //   enforce: true,
        // },
      },
    },
  },
  devtool: isDev ? 'source-map' : '',
  module: {
    rules: [
      // TypeScript
      {
        test: /\.ts$/,
        exclude: /node_modules|vue\/src/,
        loader: 'ts-loader',
        options: {
          appendTsSuffixTo: [/\.vue$/],
        },
      },
      {
        test: /\.js$/,
        exclude: /node_modules/,
        loader: 'babel-loader',
        options: {
          presets: [
            '@babel/preset-env'
          ]
        }
      },
      {
        test: /\.pug$/,
        oneOf: [
          {
            resourceQuery: /^\?vue/,
            use: ['pug-plain-loader'],
          },
          {
            use: ['pug-loader'],
          }
        ],
      },
      {
        // Vue
        test: /\.vue$/,
        loader: 'vue-loader',
        options: {
          loader: {
            scss: 'vue-style-loader!css-loader!sass-loader',
          },
          postcss: {
            config: {
              path: PATHS.webpack,
            },
          },
        },
      },
      {
        // Fonts
        test: /\.(woff(2)?|ttf|eot|svg)(\?v=\d+\.\d+\.\d+)?$/,
        loader: 'file-loader',
        options: {
          name: `[name].[ext]`,
          outputPath: '/assets/fonts/'
        },
      },
      {
        test: /\.(png|jpg|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              esModule: false,
               name: '[name].[ext]',
              outputPath: `${PATHS.assets}/img`,
             //publicPath: `/${PATHS.assets}img`,
            },
          },
        ],

      },
      {
        // scss
        test: /\.scss$/,
        use: [
          'style-loader',
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {sourceMap: true},
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              config: {path: `${PATHS.webpack}/postcss.config.js`},
            },
          },
          {
            loader: 'sass-loader',
            options: {
              sourceMap: true,
            },
          },
          {
            loader: 'sass-resources-loader',
            options: {
              resources: [
                './src/assets/scss/core/base.scss',
              ],
            },
          },
        ],
      },
      {
        // css
        test: /\.css$/,
        use: [
          'style-loader',
          MiniCssExtractPlugin.loader,
          {
            loader: 'css-loader',
            options: {sourceMap: true},
          },
          {
            loader: 'postcss-loader',
            options: {
              sourceMap: true,
              config: {path: `${PATHS.webpack}/postcss.config.js`},
            },
          },
        ],
      },
      // {
      //   test: /\.(html)$/,
      //   use: [
      //     {
      //       loader: 'html-loader',
      //       // options: {
      //       //   minimize: {
      //       //     removeComments: true,
      //       //     collapseWhitespace: false,
      //       //   },
      //       // },
      //     },
      //   ],
      // },
    ],
  },
  resolve: {
    alias: {
      '~': PATHS.src,
      'vue$': 'vue/dist/vue.js',
      '@': path.resolve(__dirname, '../../src'),
      '@img': path.resolve(__dirname, '../../src/assets/img/'),
    },
    extensions: ['.tsx', '.ts', '.js'],
  },

  plugins: plugins('pug'),


};
