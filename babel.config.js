module.exports = {
    plugins: [
        '@babel/plugin-syntax-dynamic-import', 
        [
            "import",
            {
                "libraryName": "element-ui",
                "styleLibraryName": "theme-chalk"
            }
        ]
    ],
    presets: [
        [
            '@babel/preset-env',
            {
                modules: false
            }
        ]
    ]
}