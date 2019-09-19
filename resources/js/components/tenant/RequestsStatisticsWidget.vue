<template>
    <card class="requests-statistics">
        <el-row v-if="data">
            <el-col :span="12">
                <small>Opened requests</small>
                {{data.opened_requests_count}}
            </el-col>
            <el-col :span="12">
                <small>Pending requests</small>
                {{data.pending_requests_count}}
            </el-col>
            <el-col :span="12">
                <small>Done requests</small>
                {{data.done_requests_count}}
            </el-col>
            <el-col :span="12">
                <small>Archived requests</small>
                {{data.archived_requests_count}}
            </el-col>
        </el-row>
    </card>
</template>

<script>
    import axios from '@/axios'
    import Card from 'components/Card'

    export default {
        components: {
            Card
        },
        data () {
            return {
                data: null
            }
        },
        async created () {
            const {data} = await axios.get(`tenants/${this.$store.getters.loggedInUser.tenant.id}/statistics`)

            this.data = data.data
        }
    }
</script>

<style lang="scss" scoped>
    .el-card.requests-statistics {
        :global(.el-card__body) {
            padding: 0;
            .el-row {
                margin: -1px;
                .el-col {
                    padding: 16px;
                    text-align: center;
                    font-size: 24px;
                    border-right: 1px darken(#fff, 8%) solid;
                    border-bottom: 1px darken(#fff, 8%) solid;
                    &:nth-of-type(2n) {
                        border-right-style: none;
                    }
                    &:nth-last-of-type(-n+2) {
                        border-bottom-style: none;
                    }
                    small {
                        display: block;
                        font-size: 64%;
                        margin-bottom: 8px;
                        color: darken(#fff, 48%);
                    }
                }
            }
        }
    }
</style>