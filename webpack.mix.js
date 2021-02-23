const mix = require('laravel-mix')
const path = require('path')

const rootDir = 'src/'
const assetsDir = rootDir + 'assets/'
const distDir = 'dist/'

mix.js(assetsDir + 'js/admin.js', distDir)
    .js(assetsDir + 'js/app.js', distDir)
    .js(assetsDir + 'js/tainacan-view-modes.js', distDir)
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
            path: path.resolve(__dirname, rootDir),
            publicPath: '/wp-content/plugins/iande/',
        },
        plugins: [
            new webpack.EnvironmentPlugin({
                NODE_ENV: process.env.NODE_ENV,
            }),
        ]
    }))
