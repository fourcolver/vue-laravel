<template>
    <div class="notifications">
        <virtual-list
            :size="20"
            :remain="16"
            :tobottom="loadMore">
            <div class="item" :class="{ unread: !notification.read_at }" v-for="notification in notifications.data" :key="notification.id">
                <p>{{notification.data.fragment}}</p>
                <small>by {{ notification.data.user_name }}</small>
                <small>{{ ago(notification.created_at) }}</small>
            </div>
            <div class="item" v-if="loading">
                <loader :visible="loading" centered />
            </div>
        </virtual-list>
    </div>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages';
    import Loader from 'components/SimpleLoader';
    import { distanceInWordsToNow } from 'date-fns';
    import VirtualList from 'vue-virtual-scroll-list';

    export default {
        components: {
            Loader,
            VirtualList
        },
        data () {
            return {
                notifications: {
                    data: []
                },
                loading: false
            };
        },
        methods: {
            ago (date) {
                return distanceInWordsToNow(date, {
                    includeSeconds: true,
                    addSuffix: 'ago'
                });
            },
            async loadMore () {
                let {
                    current_page,
                    last_page
                } = this.notifications;

                if (current_page === last_page) {
                    return;
                }

                if (!this.loading) {
                    try {
                        this.loading = true;

                        let page = current_page;

                        page++;

                        await this.$store.dispatch('notifications/get', {
                            page,
                            per_page: 5,
                            inserting: true,
                            sortedBy: 'desc',
                            orderBy: 'created_at'
                        });

                        this.$emit('onMoreLoaded');
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading = false;
                    }
                }
            }
        },
        async created () {
            try {
                this.loading = true;

                await this.$store.dispatch('notifications/get', {
                    per_page: 5,
                    sortedBy: 'desc',
                    orderBy: 'created_at'
                });

                this.notifications = this.$store.getters['notifications/get'];

            } catch (err) {
                displayError(err);
            } finally {
                this.loading = false;
            }
        }
    };
</script>

<style lang="scss" scoped>
    .notifications {
        .item {
            padding: .5em 1em;
            p {
                font-weight: bold;
                margin: 0;
            }
            small {
                &:not(:last-of-type):after {
                    content: '\2022';
                    margin-left: 4px;
                }
            }
            &.unread {
                background-color: mix(#fff, #6AC06F, 90%);
            }
            &:not(:last-of-type) {
                border-bottom: 1px darken(#fff, 8%) solid;
            }
        }
        .loader {
            position: absolute;
            bottom: 0;
        }
    }
</style>
