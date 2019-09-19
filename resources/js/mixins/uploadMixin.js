export default {
    methods: {
        base64(file, cb) {
            let reader = new FileReader();
            // Closure to capture the file information.
            reader.onload = (function (theFile) {
                return function (e) {
                    let binaryData = e.target.result;
                    //Converting Binary Data to base 64
                    let base64String = window.btoa(binaryData);
                    //showing file converted to base64
                    cb(base64String)
                };
            })(file);
            // Read in the image file as a data URL.
            reader.readAsBinaryString(file);
        },
    }
}