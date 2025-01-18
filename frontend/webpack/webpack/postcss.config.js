const sortCSSmq = require('sort-css-media-queries');

module.exports = {
  plugins: [

    require('autoprefixer')({
      grid: true,
    }),

    require('css-mqpacker')({
      sort: sortCSSmq,
    }),
    require('cssnano')({
      preset: [
        'default', {
          discardComments: {
            removeAll: true,
          },
        },
      ],
    }),
    // require('doiuse')({
    //   ignore: ['rem', 'Opera Mini'],
    //   ignoreFiles: ['**/normalize.css'],
    //   // onFeatureUsage: function(usageInfo) { }
    //   onFeatureUsage(info) {
    //     const selector = info.usage.parent.selector;
    //     const property = `${info.usage.prop}: ${info.usage.value}`;
    //
    //     let status = info.featureData.caniuseData.status.toUpperCase();
    //
    //     if (info.featureData.missing) {
    //       status = 'NOT SUPPORTED'.red;
    //     } else if (info.featureData.partial) {
    //       status = 'PARTIAL SUPPORT'.yellow;
    //     }
    //
    //     console.log(`\n${status}:\n\n ${selector} {\n ${property};\n }\n`);
    //   },
    // }),
  ],
};


