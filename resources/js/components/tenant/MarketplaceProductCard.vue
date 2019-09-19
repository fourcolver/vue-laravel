<template>
    <div class="product" v-on="$listeners">
        <div class="media">
            <template v-if="data.media[0]">
                <el-image :src="data.media[0].url" fit="cover" :alt="data.media[0].name" :lazy="lazyImage">
                <!-- :scroll-container="lazyImageScrollContainer -->
                    <div slot="error" class="error" style="color: red;">
                        <i class="el-icon-document-delete" />
                    </div>
                    <div slot="placeholder" class="placeholder">
                        <i class="el-icon-loading"></i>
                    </div>
                </el-image>
            </template>
            <placeholder :src="require('img/5c98a90bb5c05.png')" :size="112" v-else />
            <el-tag class="price" effect="dark" :type="isFree ? 'success':'info'" disable-transitions>
                <template v-if="isFree">Free</template>
                <template v-else>
                    {{data.price.split('.')[0]}}.{{data.price.split('.')[1]}}
                    CHF
                </template>
            </el-tag>
        </div>
        <div class="content">
            <div class="title">{{data.title}}</div>
            <small class="time">
                added on {{formatDatetime(data.published_at)}}
            </small>
            <reactions :id="data.id" type="products" counter>
                <div><i class="ti-comments" /> {{data.comments_count}}</div>
                <div><i class="ti-gallery" /> {{data.media.length}}</div>
            </reactions>
        </div>
    </div>
</template>

<script>
    import Reactions from 'components/Reactions'
    import Placeholder from 'components/Placeholder'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import {format} from 'date-fns'

    export default {
        mixins: [FormatDateTimeMixin],
        props: {
            data: {
                type: Object,
                required: true
            },
            lazyImage: {
                type: Boolean,
                default: true
            },
            lazyImageScrollContainer: {}
        },
        components: {
            Reactions,
            Placeholder
        },
        computed: {
            isFree () {
                return !+(this.data.price || '0.00').replace(/\D/g, '') || this.data.type == (Object.entries(this.$constants.products.type).find(([_, name]) => name === 'giveaway') || [])[0]
            }
        }
    }
</script>

<style lang="scss" scoped>
    .product {
        background-color: #fff;
        border: 1px darken(#fff, 12%) solid;
        border-radius: 6px;
        // overflow: hidden;
        // will-change: transform;

        .media {
            width: 100%;
            overflow: hidden;
            padding-top: 100%;
            position: relative;
            border-bottom: 1px darken(#fff, 12%) solid;
            will-change: transform;

            .el-image,
            > .placeholder  {
                position: absolute;
                top: 0;
                left: 0;
                bottom: 0;
                right: 0;
            }

            .el-image {
                .error,
                .placeholder {
                    font-size: 20px;
                }

                .placeholder {
                    width: 100%;
                    height: 100%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
            }

            .el-tag.price {
                position: absolute;
                left: 0;
                bottom: 0;
                margin: 8px;
                height: auto;
                font-size: 14px;
                font-weight: bold;
                padding: 4px 8px;
                border-radius: 4px;
                line-height: normal;
                text-transform: uppercase;
                box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);

                &.el-tag--info {
                    background-color: #000;
                    border-color: #000;
                }
            }
        }
        .content {
            padding: 8px;

            .title {
                flex: 1;
                font-weight: bold;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .time {
                color: darken(#fff, 40%);
            }
        }
    }
</style>