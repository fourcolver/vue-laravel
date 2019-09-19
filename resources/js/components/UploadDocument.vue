<template>
    <el-upload
        :action="''"
        :before-upload="beforeDocumentUpload"
        :drag="drag"
        :http-request="fileUpload"
        :multiple="multiple"
        :show-file-list="false"
        :accept="acceptType"
        class="avatar-uploader"
    >
        <i class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadMixin from 'mixins/uploadMixin';

    export default {
        name: "UploadDocument",
        mixins: [UploadMixin],
        props: {
            multiple: {
                type: Boolean,
                default: false
            },
            drag: {
                type: Boolean,
                default: false
            },
            acceptType: {
                type: String,
                default: ""
            }
        },
        methods: {
            beforeDocumentUpload(file) {
                const isLt2M = file.size / 1024 / 1024 < 16;

                if (!isLt2M) {
                    displayError({
                        success: false,
                        message: 'Document size can not exceed 16MB!'
                    });
                }

                return isLt2M;
            },
            fileUpload(e) {
                let file = {
                    raw: e.file,
                    name: e.file.name,
                    src: ''
                };
                this.base64(e.file, (dataUrl) => {
                    file.src = dataUrl;
                    this.$emit("fileUploaded", file);
                });
            }
        }
    }
</script>

<style lang="scss">
    .drag-custom {
        width: 100%;

        .el-upload-dragger {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
        }

        .el-upload {
            width: 100%;
        }
    }
</style>
