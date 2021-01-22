const mix = require('laravel-mix');

const rootDir = 'plugins/iande/'
const assetsDir = rootDir + 'assets/'
const distDir = rootDir + 'dist/'

mix.js(assetsDir + 'js/admin.js', distDir)
    .js(assetsDir + 'js/app.js', distDir)
    .sass(assetsDir + 'scss/admin.scss', distDir)
    .sass(assetsDir + 'scss/app.scss', distDir)
    .options({
        processCssUrls: false,
    })
    .vue({
        compilerOptions: {
            whitespace: 'condense',
        },
        version: 2,
    })
    .webpackConfig(webpack => ({
        output: {
            chunkFilename: distDir + '[name].js',
            publicPath: '/wp-content/',
        },
        plugins: [
            new webpack.EnvironmentPlugin({
                BUILD: 'web',
                NODE_ENV: process.env.NODE_ENV,
            }),
        ]
    }))
