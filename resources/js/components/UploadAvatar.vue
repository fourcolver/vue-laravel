<template>
    <el-upload
            class="avatar-uploader"
            :action="''"
            :show-file-list="false"
            :before-upload="beforeAvatarUpload"
            :http-request="imageUpload"
    >
        <i class="el-icon-plus avatar-uploader-icon"></i>
    </el-upload>
</template>

<script>
    import {displayError} from "../helpers/messages";
    import UploadMixin from '../mixins/uploadMixin';

    export default {
        name: "UploadAvatar",
        mixins: [UploadMixin],
        methods: {
            beforeAvatarUpload(file) {
                const isJPG = file.type === 'image/jpeg';
                const isPNG = file.type === 'image/png';
                const isLt2M = file.size / 1024 / 1024 < 2;

                if (!isJPG && !isPNG) {
                    displayError({
                        success: false,
                        message: 'Avatar picture must be JPG format!'
                    });
                }
                if (!isLt2M) {
                    displayError({
                        success: false,
                        message: 'Avatar picture size can not exceed 2MB!'
                    });
                }
                return (isJPG || isPNG) && isLt2M;
            },
            imageUpload(e) {
                this.base64(e.file, (dataUrl) => {
                    this.$emit("imageUploaded", dataUrl);
                });
                this.$emit("file", e.file);
            }
        }
    }
</script>
