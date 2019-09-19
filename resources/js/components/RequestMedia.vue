<template>
    <div class="post-media">
        <gallery :images="images" :index="image" :options="options" @close="close">
            <template slot="close">
                <i class="el-icon-close"></i>
            </template>
            <template slot="prev">
                <i class="el-icon-arrow-left"></i>
            </template>
            <template slot="next">
                <i class="el-icon-arrow-right"></i>
            </template>
        </gallery>
        <el-carousel :autoplay="false" :height="height + 'px'">
            <el-carousel-item :key="image.id" v-for="(image, imageIndex) in data">
                <img :src="image.url" style="height: 100%" @click="open(imageIndex)"/>
                <el-button @click="deleteMedia(image)" class="delete-button" size="mini" type="danger"
                           v-if="withDelete">
                    {{$t('models.request.media.delete')}}
                </el-button>
            </el-carousel-item>
        </el-carousel>
    </div>
</template>

<script>
    import galleryMixin from 'mixins/galleryMixin';

    export default {
        mixins: [galleryMixin],
        props: {
            withDelete: {
                type: Boolean,
                default: true
            }
        },
        methods: {
            deleteMedia(image) {
                if (this.withDelete) {
                    this.$emit('deleteMedia', image);
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .post-media {
        .blueimp-gallery {
            > :global(.prev),
            > :global(.next) {
                display: flex !important;
                align-items: center;
                justify-content: center;

                :global(i) {
                    font-size: 24px;
                }
            }
        }

        .blueimp-gallery > .next, .blueimp-gallery > .prev {
            background: red;
        }

        img {
            cursor: pointer;
            opacity: .9;
            transition-property: opacity, transform;
            transition-duration: .16s;
            transition-timing-function: linear;
            will-change: transform;

            &:hover {
                opacity: 1;
                transform: scale(1.04);
            }
        }
    }

    .delete-button {
        position: absolute;
        top: 0;
        right: 0;
    }
</style>
