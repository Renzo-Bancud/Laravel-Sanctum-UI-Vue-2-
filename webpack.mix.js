const mix = require('laravel-mix');

mix.copy('resources/js/assets', 'public/assets')
  .webpackConfig({
    module: {
      rules: [
        {
          test: /\.apk$/,
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'assets/apk',
          },
        },
      ],
    },
  })
  .js('resources/js/app.js', 'public/js')
  .vue()
  .sass('resources/sass/app.scss', 'public/css')
  .version();
