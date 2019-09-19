<template>
    <div class="reactions">
        <template v-if="counter">
            <div><i class="ti-thumb-up" /> {{like.likes_count}}</div>
        </template>
        <template v-else>
            <el-button type="text" :icon="likeIcon" :loading="loading.like" @click.stop="handleLike">
                <template v-if="like.liked">{{unlikeText}}</template>
                <template v-else>{{likeText}}</template>
            </el-button>
        </template>
        <slot />
    </div>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        props: {
            id: {
                type: Number,
                required: true
            },
            type: {
                type: String,
                required: true,
                validator: type => ['posts', 'products'].includes(type)
            },
            counter: {
                type: Boolean,
                default: false
            },
            likeText: {
                type: String,
                default: 'Like'
            },
            unlikeText: {
                type: String,
                default: 'Unlike'
            }
        },
        data () {
            return {
                loading: {
                    like: false
                }
            }
        },
        methods: {
            async handleLike () {
                this.loading.like = true

                try {
                    await this.$store.dispatch(this.liked ? 'likes/unlike' : 'likes/like', {
                        id: this.id,
                        type: this.type
                    })
                } catch (err) {
                    displayError(err)
                } finally {
                    this.loading.like = false
                }
            }
        },
        computed: {
            like () {
                const getters = {posts: 'posts2/get', products: 'products2/get'}

                const {liked, likes_count} = this.$store.getters[getters[this.type]].data.find(({id}) => id === this.id)

                return {
                    liked,
                    likes_count
                }
            },
            liked () {
                const getters = {posts: 'posts2/get', products: 'products2/get'}

                return this.$store.getters[getters[this.type]].data.find(({id}) => id === this.id).liked
            },
            likeIcon () {
                if (this.loading.like) {
                    return 'el-icon-loading'
                }

                return this.liked ? 'ti-thumb-down' : 'ti-thumb-up';
            }
        }
    }
</script>

<style lang="scss" scoped>
    .reactions {
        display: flex;
        align-items: center;
        > div {
            i {
                vertical-align: middle;
            }
            &:not(:last-child) {
                margin-right: 4px;
                &:after {
                    content: '\2022';
                    margin-left: 4px;
                    margin-right: 2px;
                }
            }
        }
        .el-button {
            padding: 0;
            :global(span) {
                margin-left: 5px;
            }
            &:before {
                background-color: transparent;
            }            
        }
    }
</style>