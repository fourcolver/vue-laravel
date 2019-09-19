<template>
    <card>
        <template slot="header">
            <div class="title">Latest public requests</div>
            <el-button type="text" @click="viewAll()">View all</el-button>
        </template>
        <template v-if="isLoading">
            <loader visible centered :size="20" />
        </template>
        <template v-else>
            <el-collapse v-model="active" accordion v-if="requests.data.length">
                <el-collapse-item v-for="(request, idx) in requests.data" :key="request.id" :name="idx">
                    <div slot="title" class="title">
                        <small>{{ request.category.name }}</small>
                        {{ request.title }}
                    </div>
                    {{ request.description }}
                </el-collapse-item>
            </el-collapse>
            <placeholder v-else :src="require('img/5c9d48f15dd1a.png')">
                There are no requests opened...
                <small slot="secondary">Latest public only requests will appear here...</small>
            </placeholder>
        </template>
    </card>
</template>

<script>
    // TODO - after requests refactoring -> refactor this aswell to get data from the store (argh...)
    import Card from 'components/Card'
    import {displaySuccess, displayError} from 'helpers/messages'
    import Loader from 'components/SimpleLoader'
    import Placeholder from 'components/Placeholder'

    export default {
        components: {
            Card,
            Loader,
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
                requests: {
                    data: []
                }
            }
        },
        methods: {
            viewAll () {
                this.$router.push({name: 'tenantRequests'});
            }
        },
        computed: {
            isLoading () {
                return this.loading && !this.requests.data.length;
            }
        },
        async created () {
            try {
                this.loading = true;

                const {data} = await this.$store.dispatch('getRequests', {
                    is_public: true,
                    sortedBy: 'desc',
                    orderBy: 'created_at',
                    per_page: this.limit
                })

                this.requests = data
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
                            flex-direction: column;
                            line-height: 1.48;
                            overflow: hidden;
                            font-weight: bold;
                            small {
                                font-weight: normal;
                                display: block;
                            }
                        }
                    }
                    :global(.el-collapse-item__wrap) {
                        background: transparent;
                        :global(.el-collapse-item__content) {
                            color: darken(#fff, 48%);
                            padding: 0 16px 16px 16px;
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