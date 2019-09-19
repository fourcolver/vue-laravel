<template>
    <div class="post-details" v-if="!_.isEmpty(post)">
        <el-row :gutter="10">
            <template v-if="post.tenant">
                <el-col :md="6" :sm="12">
                    <p class="post-label">{{$t('models.user.name')}}</p>
                    <p v-if="post.tenant">
                        <router-link :to="{name: 'adminTenantsEdit', params: {id: post.tenant.id}}">
                            {{post.tenant.first_name}} {{post.tenant.last_name}}
                        </router-link>
                    </p>
                </el-col>
                <el-col :md="6" :sm="12" v-if="post.tenant.building">
                    <p class="post-label">{{$t('models.tenant.building.name')}}</p>
                    <p>
                        {{post.tenant.building.name}}
                    </p>
                </el-col>
            </template>
            <template v-else>
                <el-col :md="6" :sm="12">
                    <p class="post-label">{{$t('models.user.name')}}</p>
                    <router-link :to="{name: 'adminUsersEdit', params: {id: post.user.id}}">
                        {{post.user.name}}
                    </router-link>
                </el-col>
            </template>
            <el-col :md="4" :sm="12">
                <p class="post-label">{{$t('models.post.type.label')}}</p>
                <p>
                    {{this.$t(`models.post.type.${postConstants.type[post.type]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="post-label">{{$t('models.post.visibility.label')}}</p>
                <p>
                    {{this.$t(`models.post.visibility.${postConstants.visibility[post.visibility]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="post-label">{{$t('models.post.status.label')}}</p>
                <p>
                    {{this.$t(`models.post.status.${postConstants.status[post.status]}`)}}
                </p>
            </el-col>
            <el-col :md="2" :sm="12">
                <p class="post-label">{{$t('models.post.likes')}}</p>
                <p>
                    {{post.likes_counter}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="post-label">{{$t('models.post.published_at')}}</p>
                <p>
                    {{post.published_at}}
                </p>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <p class="post-label">{{$t('models.post.content')}}</p>
                <p>
                    {{post.content}}
                </p>
            </el-col>
        </el-row>
        <gallery :data="post.media" :withDelete="false" v-if="post.media && post.media.length"></gallery>
    </div>
</template>

<script>
    import Gallery from 'components/RequestMedia';


    export default {
        name: "PostDetails",
        components: {
            Gallery
        },
        props: {
            post: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                postConstants: this.$store.getters['application/constants'].posts,
            }
        }
    }
</script>

<style scoped>
    .post-label {
        color: #409EFF;
    }
</style>
