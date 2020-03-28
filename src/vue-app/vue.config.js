const path = require("path");

module.exports = {
    outputDir: path.resolve(__dirname, "../../dist/public_html"),
    lintOnSave: false
    /*
    //TODO: исправить баг, если будет использоваться loaderOptions
    ,
    css: {
        loaderOptions: {
            sass: {
                prependData: "@import '@/assets/variables.scss';"
            }
        }
    }
     */
};
