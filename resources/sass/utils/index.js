const fs = require("fs-extra")
const path = require("path")
const klawSync = require("C:/Users/allocen/AppData/Roaming/npm/node_modules/klaw-sync")


stream =fs.createWriteStream("utils.scss")
klawSync(__dirname, {nodir: true}).forEach(item => {
    stream.write(`@import "${item.path}"\r\n`)
})

stream.end()