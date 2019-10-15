module.exports = {
    entry: {
        public: './assets/src/public.js',
    },
    output: {
        filename: "[name].js",
    },
    module: {
        rules: [
        {
            test: /\.(js|jsx)$/,
            exclude: /(node_modules)/,
            loader: 'babel-loader',
            query: {
                presets: ["@babel/preset-env"],
            },
        },
        ],
    },
};
