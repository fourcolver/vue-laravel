<template>
    <div class="listing" v-loading="loading">
        <el-table
            :data="list"
            style="width: 100%">
            <el-table-column
                :key="column.prop"
                :label="column.label"
                :prop="column.prop"
                v-for="column in columns"
                v-if="!column.i18n"
            >
            </el-table-column>
            <el-table-column
                :key="column.prop"
                :label="column.label"
                v-for="column in columns"
                v-if="column.i18n"
            >
                <template slot-scope="scope">
                    <span :style="{background: column.withBadge(scope.row[column.prop])}" class="badge"
                          v-if="column.withBadge">
                        {{column.i18n(scope.row[column.prop])}}
                    </span>
                    <template v-else>
                        {{column.i18n(scope.row[column.prop])}}
                    </template>
                </template>
            </el-table-column>
            <el-table-column
                :key="index"
                :width="action.width"
                align="right"
                v-for="(action, index) in actions"
            >
                <template slot-scope="scope">
                    <el-button
                        :icon="button.icon"
                        :key="button.title"
                        :style="button.style"
                        :type="button.type"
                        @click="button.onClick(scope.row)"
                        size="mini"
                        v-for="button in action.buttons"
                        v-if="!button.tooltipMode">{{button.title}}
                    </el-button>
                    <el-tooltip
                        :content="button.title"
                        :key="button.title"
                        class="item" effect="light" placement="top-end"
                        v-for="button in action.buttons"
                        v-if="button.tooltipMode">
                        <el-button
                            :icon="button.icon"
                            :style="button.style"
                            :type="button.type"
                            @click="button.onClick(scope.row)"
                            size="mini"
                        >
                        </el-button>
                    </el-tooltip>
                </template>
            </el-table-column>
        </el-table>
        <div v-if="meta.current_page < meta.last_page">
            <el-button @click="loadMore" size="mini" style="margin-top: 15px" type="text">{{$t('loadMore')}}</el-button>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            filter: {
                type: String,
                required: true
            },
            filterValue: {
                type: Number,
                required: true
            },
            fetchAction: {
                type: String,
                required: true
            },
            columns: {
                type: Array,
                default() {
                    return [];
                }
            },
            actions: {
                type: Array,
                default() {
                    return [];
                }
            }
        },
        data() {
            return {
                list: [],
                meta: {},
                loading: false
            }
        },
        async created() {
            await this.fetch();
        },
        methods: {
            async fetch(page = 1) {
                this.loading = true;
                try {
                    const resp = await this.$store.dispatch(this.fetchAction, {
                        [this.filter]: this.filterValue,
                        per_page: 5,
                        page
                    });
                    this.meta = _.omit(resp.data, 'data');
                    if (page === 1) {
                        this.list = resp.data.data;
                    } else {
                        this.list.push(...resp.data.data);
                    }
                } catch (e) {
                    console.log(e);
                } finally {
                    this.loading = false;
                }
            },
            loadMore() {
                if (this.meta.current_page < this.meta.last_page) {
                    this.fetch(this.meta.current_page + 1);
                }
            }
        }
    }
</script>

<style scoped>
    .badge {
        color: #fff;
        display: flex;
        width: 100px;
        font-size: 12px;
        justify-content: center;
        border-radius: 25px;
    }
</style>
