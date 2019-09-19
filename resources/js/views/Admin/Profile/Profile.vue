<template>
    <el-card>
        <el-form label-width="128px">
            <el-form-item :label="$t('models.user.profile_image')">
                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
            </el-form-item>
            <el-form-item>
                <el-button @click="upload" icon="ti-save" type="primary">{{$t('actions.upload')}}</el-button>
            </el-form-item>
        </el-form>
    </el-card>
</template>

<script>
    import Cropper from 'components/Cropper';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';

    export default {
        name: 'AdminSettingsProfile',
        components: {
            Cropper
        },
        data() {
            return {
                image: ''
            }
        },
        methods: {
            ...mapActions(['uploadAvatar', 'me']),
            cropped(e) {
                this.image = e;
            },
            upload() {
                this.uploadAvatar({image_upload: this.image})
                    .then(r => {
                        displaySuccess(r);
                        this.me();
                    })
                    .catch(err => displayError(err));
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        border-radius: 6px;
        box-shadow: 0 1px 3px transparentize(#000, .88),
        0 1px 2px transparentize(#000, .76);
        position: relative;

        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51620031_23843322451160669_1044943222171762688_n.png');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: bottom right;
        }

        .el-form {
            max-width: 512px;

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
