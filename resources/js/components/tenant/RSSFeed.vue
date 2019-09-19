<template>
    <card>
        <div slot="header" v-if="title">{{ title }}</div>
        <el-collapse v-model="active" accordion>
            <el-collapse-item v-for="(item, idx) in items" :key="item.guid" :name="idx">
                <div slot="title" class="title">
                    <div>{{ item.title }}</div>
                    <small>
                        {{ item.pubDate }}
                    </small>
                </div>
                {{ item.contentSnippet | empty }}
                <el-button size="mini" round @click="readMore(item.link)">Read more</el-button>
            </el-collapse-item>
        </el-collapse>
    </card>
</template>

<script>
    import Card from 'components/Card';
    import VueRssFeed from 'vue-rss-feed';
    import axios from '@/axios';
    import {displaySuccess, displayError} from 'helpers/messages';
    import {API_BASE_URL} from '@/config';
    import Parser from 'rss-parser';
    import { distanceInWordsToNow } from 'date-fns'

    export default {
        props: {
            limit: {
                type: Number,
                default: 5
            },
            title: {
                type: String,
                default: ''
            }
        },
        components: {
            VueRssFeed,
            Card
        },
        filters: {
            empty (value) {
                if (!value) {
                    return 'No description available.';
                }

                return value;
            }
        },
        data () {
            return {
                active: 0,
                items: []
            };
        },
        methods: {
            readMore (link) {
                window.open(link, '_blank');
            }
        },
        async created () {
            let parser = new Parser({
                headers: {
                    Authorization: `Bearer ${localStorage.getItem('token')}`
                }
            });

            try {
                const {items} = await parser.parseURL(API_BASE_URL + 'news/rss.xml');

                this.items = items.filter((item, idx) => {
                    item.pubDate = distanceInWordsToNow(item.pubDate, {
                        includeSeconds: true,
                        addSuffix: 'ago'
                    });

                    if (idx <= this.limit - 1) {
                        return item;
                    }
                });
            } catch (err) {
                displayError(err);
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__header) {
            padding-bottom: 16px;
        }
        :global(.el-card__body) {
            padding: 0;
            .el-collapse {
                border-style: none;
                .el-collapse-item {
                    :global(.el-collapse-item__header) .title {
                        display: flex;
                        flex-direction: column;
                        line-height: 1.48;
                        overflow: hidden;
                        padding: 0 16px;
                        div {
                            flex: 1;
                            font-weight: bold;
                            white-space: nowrap;
                            overflow: hidden;
                            text-overflow: ellipsis;
                        }
                    }
                    :global(.el-collapse-item__content) {
                        color: darken(#fff, 56%);
                        padding: 0 16px 16px 16px;
                        .el-button {
                            width: 100%;
                            margin-top: 1em;
                        }
                    }
                }
            }
        }
    }
</style>
