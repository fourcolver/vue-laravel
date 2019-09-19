<template>
    <div class="post">
        <heading icon="ti-announcement" title="News" description="Sed placerat volutpat mollis." />
        <el-row :gutter="24">
            <el-col :span="16">
                <post :data="data" v-if="data" />
            </el-col>
            <el-col :span="8">
                <rss-feed title="Blick.ch News"/>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Post from 'components/tenant/Post'
    import Heading from 'components/Heading'
    import RssFeed from 'components/tenant/RSSFeed'
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        components: {
            Post,
            Heading,
            RssFeed
        },
        data () {
            return {
                data: null
            }
        },
        async created () {
            try {
                const id = this.$route.params.id

                await this.$store.dispatch('posts2/get', {id})

                this.data = this.$store.getters['posts2/getById'](id)
            } catch (err) {
                this.$router.replace({name: 'tenantPosts'})
            }
        }
    }
</script>

<style lang="scss" scoped>
    .post {
        :global(.heading) {
            margin-bottom: 1em;
        }
        .el-row {
            .el-col {
                &:nth-child(1) {
                    max-width: 640px;
                }
                &:nth-child(2) {
                    max-width: 448px;
                }
            }
        }
    }
</style>