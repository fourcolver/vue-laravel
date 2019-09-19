<template>
    <div class="product-details">
        <media-gallery-carousel ref="media-gallery-carousel" :media="data.media" :autoplay="false" />
        <el-tabs value="overview">
            <el-tab-pane name="overview" label="Overview">
                <div class="container">
                    <div class="heading">
                        {{data.title}}
                        <small class="time">
                            added on {{formatDatetime(data.published_at)}}
                        </small>
                    </div>
                    <el-tag class="type" size="mini" disable-transitions>{{typeName}}</el-tag>
                    <el-divider />
                    <div class="price">
                        <span v-if="isFree">
                            <div class="content">Free</div>
                        </span>
                        <template v-else>
                            <div class="title">Price</div>
                            <div class="content">
                                {{data.price.split('.')[0]}}.{{data.price.split('.')[1]}}
                                CHF
                            </div>
                        </template>
                    </div>
                    <el-divider />
                    <read-more class="description" :text="data.content" :max-chars="512" more-str="Read more" less-str="Read less" />
                    <el-divider />
                    <reactions :id="data.id" type="products" />
                </div>
                <el-divider />
                <el-alert class="contact" type="info" :description="data.contact" center v-if="showContactInformations" :closable="false" />
                <template v-else>
                    <el-button type="primary" round @click="showContactInformations = true">Get in touch</el-button>
                    <small class="hint">Use the above button to get to know how you may contact the seller in order to get this product.</small>
                </template>
            </el-tab-pane>
            <el-tab-pane name="comments" label="Comments" lazy>
                <chat :id="data.id" type="product" size="100%" max-size="512px" autofocus />
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    import Chat from 'components/Chat2'
    import Reactions from 'components/Reactions'
    import MediaGalleryCarousel from 'components/MediaGalleryCarousel'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'

    export default {
        mixins: [FormatDateTimeMixin],
        props: {
            data: {
                type: Object,
                required: true
            }
        },
        components: {
            Chat,
            Reactions,
            MediaGalleryCarousel
        },
        data () {
            return {
                showContactInformations: false
            }
        },
        computed: {
            typeName () {
                return this.$constants.products.type[this.data.type]
            },
            isFree () {
                return !+(this.data.price || '0.00').replace(/\D/g, '') || this.data.type == (Object.entries(this.$constants.products.type).find(([_, name]) => name === 'giveaway') || [])[0]
            }
        }
    }
</script>

<style lang="scss" scoped>
    .product-details {
        display: grid;
        grid-template-columns: 1fr minmax(auto, 384px);

        .el-tabs {
            z-index: 2;
            padding: 24px;
            box-shadow: 0 24px 40px transparentize(#000, .68), 0 15px 12px transparentize(#000, .76);

            :global(.el-tabs__content) {
                overflow: visible;

                #pane-overview {
                    .container {
                        .heading {
                            color: #000;
                            font-size: 18px;
                            word-break: break-word;

                            .time {
                                display: block;
                                font-size: 64%;
                                color: darken(#fff, 40%);
                            }
                        }

                        .type {
                            text-transform: uppercase;
                            margin-top: 6px;
                            font-weight: bold;
                        }

                        .price {
                            display: flex;
                            flex-direction: column;

                            .title {
                                color: #000;
                                font-size: 16px;
                            }

                            .content {
                                font-size: 20px;
                                font-weight: bold;
                                color: #6AC06F;
                            }
                        }

                        .description {
                            :global(p) {
                                margin: 0;
                                white-space: pre-wrap;
                                word-break: break-word;
                            }
                            :global(a) {
                                color: #6AC06F;
                                text-decoration: none;
                                transition: color .48s;

                                &:focus,
                                &:hover {
                                    color: lighten(#6AC06F, 16%);
                                }
                            }
                        }

                        .el-alert.contact :global(.el-alert__description) {
                            margin: 0;
                            font-size: 14px;
                            font-weight: bold;
                        }
                    }

                    .el-button {
                        width: 100%;

                        & + .hint {
                            display: block;
                            line-height: 1.32;
                            text-align: center;
                            word-break: break-word;
                            margin-top: 12px;
                        }
                    }

                    .el-divider {
                        margin: 12px 0;
                    }
                }

                #pane-comments {
                    .chat {
                        margin: -16px;

                        :global(.placeholder) {
                            margin: 16px 0;
                        }
                    }
                }
            }
        }
    }
</style>