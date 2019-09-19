<template>
    <div class="chat">
        <comments-list ref="comments" :id="id" :type="type" :limit="limit" reversed with-scroller :show-children="false" :style="{height: size, minHeight: minSize, maxHeight: maxSize}" />
        <add-comment ref="addComment" :id="id" :type="type" :show-templates="showTemplates" />
    </div>
</template>

<script>
    import AddComment from './AddComment'
    import CommentsList from './CommentsList'

    export default {
        props: {
            id: {
                type: Number,
                required: true
            },
            type: {
                type: String,
                required: true,
                validator: type => ['post', 'product', 'request', 'conversation'].includes(type)
            },
            size: {
                type: String,
                default: '320px'
            },
            minSize: {
                type: String,
                default: '84px'
            },
            maxSize: {
                type: String,
                default: '320px'
            },
            limit: {
                type: Number,
                default: 50
            },
            autofocus: {
                type: Boolean,
                default: false
            },
            showTemplates: {
                type: Boolean,
                default: false
            }
        },
        components: {
            AddComment,
            CommentsList
        },
        methods: {
            focusOnAddComment() {
                this.$refs.addComment.focus()
            }
        },
        mounted () {
            if (this.autofocus) {
                this.focusOnAddComment()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .chat {
        height: 100%;
        color: lighten(#000, 32%);
        display: flex;
        flex-direction: column;
        .comments-list {
            :global(.vue-recycle-scroller) {
                padding: 16px;
                padding-bottom: 8px;
                :global(.vue-recycle-scroller__slot) {
                    :global(.el-divider) {
                        margin-top: 8px;
                    }
                }
            }
        }
        .add-comment {
            width: auto;
            margin: 16px;
            margin-top: 0;
        }
    }
</style>
