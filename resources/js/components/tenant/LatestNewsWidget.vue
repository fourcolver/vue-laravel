<template>
    <card>
        <template slot="header">
            <div class="title">Latest news</div>
            <el-button type="text" @click="viewAll()">View all</el-button>
        </template>
        <template v-if="isLoading">
            <loader visible centered :size="20" />
        </template>
        <template v-else>
            <el-collapse v-model="active" accordion v-if="posts.data.length">
                <el-collapse-item v-for="(post, idx) in posts.data" :key="post.id" :name="idx">
                    <div slot="title" class="title">
                        <avatar :name="post.user.name" :size="32" />
                        <div>
                            {{ post.user.name }}
                            <small>
                                {{ ago(post.published_at) }}
                            </small>
                        </div>
                    </div>
                    <div class="content">{{ post.content }}</div>
                    <reactions :id="post.id" type="posts" counter>
                        <div><i class="ti-comments" /> {{ post.comments_count }}</div>
                    </reactions>
                    <el-button type="primary" size="mini" round @click="view(post)">View</el-button>
                </el-collapse-item>
            </el-collapse>
            <placeholder v-else :src="require('img/5c9d6e6c73f70.png')">
                There are not news yet...
                <small slot="secondary">Latest news available will be listed here</small>
            </placeholder>
        </template>
    </card>
</template>

<script>
    // TODO - after news refactoring -> refactor this aswell to get data from the store (argh...)
    import Avatar from 'components/Avatar';
    import Card from 'components/Card'
    import {displaySuccess, displayError} from 'helpers/messages'
    import { distanceInWordsToNow } from 'date-fns'
    import Loader from 'components/SimpleLoader'
    import Reactions from 'components/Reactions'
    import Placeholder from 'components/Placeholder'

    export default {
        components: {
            Avatar,
            Card,
            Loader,
            Reactions,
            Placeholder
        },
        props: {
            limit: {
                type: Number,
                default: 5
            }
        },
        data () {
            return {
                active: 0,
                loading: false,
                posts: {
                    data: []
                }
            }
        },
        methods: {
            view ({id}) {
                this.$router.push({name: 'tenantPost', params: {
                    id
                }})
            },
            ago (date) {
                if (date) {
                    return distanceInWordsToNow(date, {
                        includeSeconds: true,
                        addSuffix: 'ago'
                    });
                }
            },
            viewAll () {
                this.$router.push({name: 'tenantPosts'});
            }
        },
        computed: {
            isLoading () {
                return this.loading && !this.posts.data.length;
            }
        },
        async created () {
            const postStatuses = this.$constants.posts.status
            const publishedStatus = Object.keys(postStatuses).find(k => postStatuses[k] === 'published')

            try {
                this.loading = true;

                await this.$store.dispatch('posts2/get', {
                    status: +publishedStatus,
                    feed: 1,
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    per_page: this.limit
                })

                this.posts = this.$store.getters['posts2/get']
            } catch (err) {
                displayError(err)
            } finally {
                this.loading = false;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__header) {
            .title {
                flex: auto;
                overflow: hidden;
                min-width: 0;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
            .el-button {
                padding: 0;
            }
        }
        :global(.el-card__body) {
            .el-collapse {
                margin: -16px;
                border-style: none;
                .el-collapse-item {
                    :global(.el-collapse-item__header) {
                        background: transparent;
                        padding: 0 16px;
                        .title {
                            flex: auto;
                            display: flex;
                            align-items: center;
                            line-height: 1.48;
                            overflow: hidden;
                            > div:last-of-type {
                                flex: 1;
                                white-space: nowrap;
                                overflow: hidden;
                                text-overflow: ellipsis;
                                margin-left: 8px;
                                font-weight: bold;
                                small {
                                    font-weight: normal;
                                    display: block;
                                }
                            }
                        }
                    }
                    :global(.el-collapse-item__wrap) {
                        background: transparent;
                        :global(.el-collapse-item__content) {
                            color: darken(#fff, 48%);
                            padding: 0 16px 16px 16px;
                            :global(.reactions) {
                                float: left;
                            }
                            .el-button {
                                float: right;
                            }
                            &:after {
                                content: '';
                                display: table;
                                clear: both;
                            }
                        }
                    }
                    &:last-child :global(.el-collapse-item__header) {
                        border-bottom-style: none;
                    }
                }
            }
        }
    }
</style>