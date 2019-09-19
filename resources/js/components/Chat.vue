<template>
    <div class="request-comments-list">
        <virtual-list
                :debounce="56"
                :item="comment"
                :itemdata="comments.data"
                :itemprop="commentProps"
                :remain="16"
                :size="24"
                :totop="getMore"
                ref="vsl"
                v-chat-scroll="scrollOptions"
                v-loading="loading"
                wclass="virtual-list"
        />
        <div class="empty" v-if="isEmpty">
            <i class="el-icon-info"/>
            This chat is empty...
        </div>
    </div>
</template>

<script>
    import Comment from './ChatComment';
    import {displaySuccess, displayError} from 'helpers/messages';
    import VirtualList from 'vue-virtual-scroll-list';

    export default {
        props: {
            modelId: {
                type: Number,
                required: true
            },
            config: {
                type: Object,
                default() {
                    return {
                        get: '',
                        fetch: '',
                        meta: ''
                    }
                }
            }
        },
        components: {
            VirtualList
        },
        data() {
            return {
                loading: false,
                comment: Comment,
                comments: {
                    data: []
                },
                scrollOptions: {
                    always: false,
                    smooth: true,
                    scrollonremoved: true
                }
            };
        },
        methods: {
            async get(params = {
                per_page: 10,
                sortedBy: 'desc',
                orderBy: 'created_at'
            }) {
                this.loading = true;

                try {
                    await this.$store.dispatch(this.config.fetch, {id: this.modelId, ...params});

                    this.comments = this.$store.getters[this.config.get](this.modelId);
                } catch (err) {
                    displayError(err);
                } finally {
                    setTimeout(() => this.loading = false);
                }
            },

            getVariableHeight(index) {
                this.count++;

                let target = this.comments.data[index];

                return target && target.height;
            },

            commentProps(index, data) {
                return {
                    key: data.id,
                    props: {
                        data,
                        reversed: data.reversed,
                        modelId: this.modelId,
                        config: {
                            update: this.config.update
                        }
                    }
                };
            },
            async getMore() {
                let {
                    current_page,
                    last_page
                } = this.$store.getters[this.config.meta](this.modelId);

                if (current_page === last_page) {
                    return;
                }

                if (!this.loading) {
                    let page = current_page;

                    page++;

                    await this.get({
                        id: this.modelId,
                        page,
                        per_page: 10,
                        inserting: true,
                        sortedBy: 'desc',
                        orderBy: 'created_at'
                    });

                    setTimeout(() => {
                        this.$refs.vsl.$el.scrollBy({
                            top: 51,
                            behavior: 'smooth'
                        });
                    });
                }
            }
        },
        computed: {
            isEmpty() {
                return !this.loading && !this.comments.data.length;
            }
        },
        async created() {
            await this.get(this.modelId);
        }
    }
</script>

<style lang="scss" scoped>
    .request-comments-list {
        position: relative;
        will-change: height;
        display: flex;
        flex: 1;
        flex-direction: column;

        .empty {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            color: darken(#fff, 56%);

            i {
                font-size: 48px;
                margin: 12px;
            }
        }

        :global(.virtual-list) {
            padding: 1em;

            :global(.comment) {
                margin: .5em 0;
            }
        }
    }
</style>
