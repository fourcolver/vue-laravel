<template>
    <div>
        <upload-avatar @file="upload"/>
        <vue-croppie
            :boundary="boundary"
            :enableOrientation="orientation"
            :enableResize="resize"
            :enableZoom="zoom"
            :showZoomer="showZoomer"
            :viewport="computedViewport"
            @result="result"
            @update="update"
            ref="croppieRef"
            v-show="uploaded">
        </vue-croppie>
    </div>
</template>

<script>
    import UploadAvatar from 'components/UploadAvatar';
    import 'croppie/croppie.css'

    export default {
        components: {
            UploadAvatar
        },
        props: {
            boundary: {
                type: Object,
                default: () => {
                    return {
                        height: 400,
                    }
                }
            },
            viewport: {
                type: Object,
                default: () => {
                    return {
                        width: 300,
                        height: 300
                    }
                }
            },
            zoom: {
                type: Boolean,
                default: true
            },
            orientation: {
                type: Boolean,
                default: true
            },
            showZoomer: {
                type: Boolean,
                default: false
            },
            resize: {
                type: Boolean,
                default: true
            },
            viewportType: {
                type: String,
                default: 'square'
            }
        },
        data() {
            return {
                uploaded: false
            }
        },
        methods: {
            upload(binaryfile) {
                this.uploaded = true;
                this.bind(binaryfile);
            },
            result(output) {
                this.$emit("cropped", output.split("base64,")[1]);
            },
            update(options) {
                this.$refs.croppieRef.result({
                    ...options,
                    quality: 1,
                    imageSmoothingQuality: 'high',
                    size: 'original',
                    type: 'canvas',
                    imageSmoothingEnabled: true
                });
            },
            bind(src) {
                const url = window.URL.createObjectURL(src);
                this.$refs.croppieRef.bind({
                    url
                });
            },
        },
        computed: {
            computedViewport() {
                return {...this.viewport, type: this.viewportType};
            }
        }
    }
</script>
