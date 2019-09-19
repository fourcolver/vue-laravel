<template>
    <div class="media-gallery-carousel" v-if="media.length">
        <gallery :images="images" :index="galleryIndex" :options="galleryOptions" @close="gallleryClose" />
        <el-carousel v-bind="$attrs" v-on="$listeners" :arrow="images.length <= 1 ? 'never' : 'hover'">
            <el-carousel-item v-for="(url, idx) in images" :key="idx">
                <div class="el-carousel-actions">
                     <div class="el-icon-zoom-in" @click="openImage(idx)"></div>
                </div>
                <el-image :src="url" fit="cover" >
                    <div slot="error" class="error" style="color: red;">
                        <i class="el-icon-document-delete" />
                    </div>
                    <div slot="placeholder" class="placeholder el-icon-loading"></div>
                </el-image>
            </el-carousel-item>
        </el-carousel>
    </div>
    <placeholder :src="require('img/5c98a90bb5c05.png')" v-else-if="!media.length && usePlaceholder" />
</template>

<script>
    import Gallery from './MediaGallery'
    import Placeholder from './Placeholder'

    export default {
        props: {
            media: {
                type: Array,
                default: () => ([])
            },
            galleryOptions: {
                type: Object,
                default: () => ({})
            },
            usePlaceholder: {
                type: Boolean,
                default: true
            }
        },
        components: {
            Gallery,
            Placeholder
        },
        data () {
            return {
                galleryIndex: null,
                sizes: {
                    minWidth: 0,
                    maxHeight: 0
                },
            }
        },
        methods: {
            openImage (idx) {
                this.galleryIndex = idx
            },
            gallleryClose () {
                this.galleryIndex = null
            },
            isImage (file) {
                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(file.name.split('.').pop())
            },
            resizeImage (x, y, percentage, minimum) {
                if (x > y) {
                    const ratio = y / x

                    const rx = (x - minimum) * (percentage / 100)
                    const ry = (y - minimum * ratio) * (percentage / 100)

                    return [rx + minimum, ry + minimum * ratio]
                } else {
                    const ratio = x / y

                    const rx = (x - minimum * ratio) * (percentage / 100)
                    const ry = (y - minimum) * (percentage / 100)

                    return [rx + minimum * ratio, ry + minimum]
                }
            }
        },
        computed: {
            images () {
                return this.media.map(file => {
                    if (this.isImage(file)) {
                        return file.url
                    }
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .media-gallery-carousel .el-carousel {
        height: 100%;

        :global(.el-carousel__container) {
            height: 100%;

            :global(.el-carousel__item) {
                will-change: transform;

                .el-carousel-actions {
                    background-color: transparentize(#000, .5);
                    position: absolute;
                    z-index: 1;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    opacity: 0;
                    transition: opacity .32s cubic-bezier(.17,.67,1,1.23);

                    div {
                        color: #fff;
                        font-size: 20px;
                        transition-property: color, font-size;
                        transition-duration: .24s;
                        opacity: .72;
                        cursor: pointer;

                        &:hover {
                            opacity: 1;
                        }
                    }
                }

                .el-image {
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;

                    .error,
                    .placeholder  {
                        font-size: 20px;
                    }

                    .placeholder {
                        background-color: #fff;
                        border-radius: 50%;
                        border: 4px #fff solid;
                    }
                }

                &:hover .el-carousel-actions {
                    opacity: 1;
                }
            }
        }

        :global(.el-carousel__indicators) {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;

            :global(.el-carousel__indicator) {
                :global(.el-carousel__button) {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                }
            }
        }
    }
</style>