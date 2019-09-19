<template>
    <div :class="['comment', {'is-reversed': reversed}]">
        <el-tooltip :content="data.user.name" :placement="reversed ? 'top-end':'top-start'" effect="dark">
            <template slot="content">
                {{data.user.name}}
                <small style="display: block;">{{ago(data.created_at)}}</small>
            </template>
            <avatar ref="avatar" :name="data.user.name" :size="32" :src="data.user.avatar" />
        </el-tooltip>
        <div ref="container" class="container">
            <el-input ref="content" :class="{'is-focused': idState.focused}" type="textarea" resize="none" v-if="idState.editing" v-model="comment" autosize :disabled="idState.loading._isVue && idState.loading.visible" :validate-event="false" @blur="idState.focused = false" @focus="idState.focused = true" @keydown.native.enter="$emit('size-hanged')" @keydown.native.alt.enter.exact="update" @keydown.native.stop.esc.exact="cancelEdit" />
            <div class="content" :class="{'empty': !comment, 'disabled': idState.loading._isVue && idState.loading.visible}" v-else>
                <div class="text">{{comment || $t('components.common.comment.deletedCommentPlaceholder')}}</div>
                <div class="actions" v-if="hasActions">
                    <el-button type="text" @click="enterEdit" v-if="data.comment">
                        <i class="icon-pencil"></i>
                    </el-button>
                    <el-button type="text" @click="remove">
                        <i class="icon-trash-empty" style="color: red;"></i>
                    </el-button>
                </div>
            </div>
        </div>
        <template v-if="idState.editing">
            <i18n path="components.common.comment.updateOrCancel" tag="div" class="extra">
                <el-tooltip :content="$t('components.common.comment.updateShortcut', {shortcut: updateKeysShortcut})" placement="bottom-start" place="update">
                    <el-button type="text" :disabled="idState.loading._isVue && idState.loading.visible" @click="update">
                        {{$t('components.common.comment.update')}}
                    </el-button>
                </el-tooltip>
                <el-tag size="mini" place="esc">{{$t('components.common.comment.esc')}}</el-tag>
                <el-button type="text" :disabled="idState.loading._isVue && idState.loading.visible" @click="cancelEdit" place="cancel">
                    {{$t('components.common.comment.cancel')}}
                </el-button>
            </i18n>
        </template>
        <el-button type="text" @click="showAddComment" v-else-if="!parentId && showChildren">
            {{$t('components.common.comment.addChildComment')}}
        </el-button>
        <div class="children" v-if="showChildren && (idState.visibleAddComment || data.children_count)">
            <el-button type="text" size="small" :loading="idState.loading.visible" @click="getChildren" v-if="data.children_count !== data.children.data.length">
                {{$tc('components.common.comment.loadMore', data.children_count - data.children.data.length)}}
            </el-button>
            <comments-list :id="id" :parent-id="data.id" :type="type" :data="data.children" :use-placeholder="false" v-if="data.children.data.length" />
            <add-comment ref="addComment" :id="id" :parent-id="data.id" :type="type" :reversed="reversed" />
        </div>
    </div>
</template>

<script>
    import Avatar from './Avatar'
    import Loader from './SimpleLoader'
    import AddComment from './AddComment'
    import AgoMixin from 'mixins/agoMixin'
    import {IdState} from 'vue-virtual-scroller'
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        mixins: [
            AgoMixin,
            IdState({
                idProp: vm => vm.data.id
            })
        ],
        props: {
            id: {
                type: Number,
            },
            parentId: {
                type: Number
            },
            type: {
                type: String,
                validator: type => ['post', 'product', 'request', 'conversation'].includes(type)
            },
            data: {
                type: Object,
                default: () => ({
                    user: {
                        name: ''
                    }
                }),
                required: true
            },
            reversed: {
                type: Boolean,
                default: false
            },
            showChildren: {
                type: Boolean,
                default: false
            }
        },
        components: {
            Avatar,
            Loader,
            AddComment,
            CommentsList: () => import('./CommentsList')
        },
        idState () {
            return {
                loading: {
                    visible: false
                },
                editing: false,
                focused: false,
                observer: null,
                commentProxy: null,
                visibleAddComment: false
            }
        },
        methods: {
            enterEdit () {
                this.idState.editing = true

                this.$nextTick(() => {
                    this.$refs.content.focus()

                    this.observer = new MutationObserver(() => this.$emit('size-changed')).observe(this.$refs.content.$el.querySelector('textarea'), {
                        attributes: true,
                        attributeFilter: ['style']
                    })
                })

                this.$emit('enter-edit')
            },
            cancelEdit () {
                this.idState.editing = false
                this.comment = this.data.comment

                this.$emit('cancel-edit')
            },
            async update () {
                if (!this.comment) {
                    return
                }

                if (this.comment === this.data.comment) {
                    return this.cancelEdit()
                }

                let loadingParams = {
                    target: this.$refs.avatar.$el
                }

                this.idState.loading = this.$loading(loadingParams)

                this.$refs.content.blur()

                let params = {
                    id: this.id,
                    commentable: this.type,
                    comment: this.comment,
                    parent_id: this.data.id
                }

                if (this.$parent.$parent.data) {
                    params.child_id = this.data.id;
                    params.parent_id = this.$parent.$parent.data.id
                }

                try {
                    await this.$store.dispatch('comments/update', params)
                } catch (error) {
                    this.comment = this.data.comment

                    displayError(error)
                } finally {
                    this.cancelEdit()

                    this.idState.loading.close()
                }
            },
            async remove () {
                this.idState.loading = this.$loading({
                    target: this.$refs.avatar.$el
                })

                let params = {
                    id: this.id,
                    commentable: this.type,
                    parent_id: this.data.id
                }

                if (this.$parent.$parent.data) {
                    params.child_id = this.data.id;
                    params.parent_id = this.$parent.$parent.data.id
                }

                try {
                    await this.$store.dispatch('comments/delete', params)
                } catch (error) {
                    displayError(error)
                } finally {
                    this.idState.loading.close()
                }
            },
            async getChildren() {
                const {
                    current_page,
                    last_page
                } = this.data.children;

                if (current_page && last_page &&
                    current_page == last_page) {
                    return
                }

                let page = current_page || 0

                page++

                this.idState.loading.visible = true

                try {
                    await this.$store.dispatch('comments/get', {
                        id: this.id,
                        parent_id: this.data.id,
                        commentable: this.type,
                        page,
                        per_page: 5,
                        sortedBy: 'desc',
                        orderBy: 'created_at'
                    })
                } catch (err) {
                    displayError(err)
                } finally {
                    this.idState.loading.visible = false
                }
            },
            showAddComment() {
                if (!this.idState.visibleAddComment) {
                    this.idState.visibleAddComment = true
                }

                this.$nextTick(() => this.$refs.addComment.focus())
            }
        },
        computed: {
            comment: {
                get () {
                    return (this.idState.commentProxy === null) ? this.data.comment : this.idState.commentProxy
                },
                set (content) {
                    this.idState.commentProxy = content
                }
            },
            hasActions() {
                return (this.data.comment || !this.data.children_count) && !this.idState.loading.visible && this.data.user_id === this.$store.getters.loggedInUser.id
            },
            updateKeysShortcut () {
                if (navigator.platform.toUpperCase().includes('MAC')) {
                    return 'option+enter'
                }

                return 'alt+enter'
            }
        },
        beforeDestroy () {
            if (this.observer) {
                this.observer.disconnect()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .comment {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        font-size: 14px;
        position: relative;

        .container {
            display: flex;
            flex-wrap: wrap;
            position: relative;
            width: calc(100% - 40px);

            .el-textarea {
                position: relative;
                margin: 2px 0;

                &:before,
                &:after {
                    content: '';
                    position: absolute;
                    width: 0;
                    height: 0;
                    border-width: 0;
                    border-style: solid;
                    border-color: transparent;
                    border-width: 0 0 8px 6px;
                    transition: border-bottom-color 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
                }

                &:before {
                    left: -6px;
                    bottom: 0;
                }

                &:after {
                    left: -4px;
                    bottom: 1px;
                }

                &.is-disabled:before {
                    border-bottom-color: #E4E7ED;
                }

                &.is-disabled:after {
                    border-bottom-color: #F5F7FB;
                }

                &:not(.is-disabled):after {
                    border-bottom-color: #fff;
                }

                &:not(.is-disabled).is-focused:before {
                    border-bottom-color: #6AC06F;
                }

                &:not(.is-disabled):not(.is-focused):before {
                    border-bottom-color: #DCDFE6;
                }

                &:not(.is-disabled):not(.is-focused):hover:before {
                    border-bottom-color: #C0C4CC;
                }

                &.is-focused :global(.el-textarea__inner)::-webkit-scrollbar-thumb {
                    background-color: #6AC06F;
                    box-shadow: inset -1px -1px 0px darken(#6AC06F, 4%), inset 1px 1px 0px darken(#6AC06F, 4%);
                }

                &:not(.is-focused) :global(.el-textarea__inner) {
                    &:hover::-webkit-scrollbar-thumb {
                        background-color: #C0C4CC;
                        box-shadow: inset -1px -1px 0px darken(#C0C4CC, 4%), inset 1px 1px 0px darken(#C0C4CC, 4%);
                    }

                    &:not(:hover)::-webkit-scrollbar-thumb {
                        background-color: #DCDFE6;
                        box-shadow: inset -1px -1px 0px darken(#DCDFE6, 4%), inset 1px 1px 0px darken(#DCDFE6, 4%);
                    }
                }

                :global(.el-textarea__inner) {
                    padding: 6px 8px;
                    border-radius: 12px;
                    max-height: 256px;
                    overflow-y: overlay;
                    overflow-x: hidden;
                    scrollbar-width: thin;
                    overscroll-behavior: contain;
                    border-bottom-left-radius: 0;
                    -webkit-appearance: none;
                    -webkit-overflow-scrolling: touch;

                    &::-webkit-scrollbar {
                        width: 14px;
                    }

                    &::-webkit-scrollbar-thumb {
                        border: 4px transparent solid;
                        background-clip: padding-box;
                        border-radius: 12px;
                    }

                    &::-webkit-scrollbar-thumb:window-inactive {
                        background-color: lighten(#6AC06F, 16%);
                    }
                }
            }

            .content {
                display: flex;
                align-items: center;
                position: relative;

                &.disabled {
                    &:before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: -6px;
                        width: calc(100% + 6px);
                        height: 100%;
                        background-color: transparentize(#fff, .1);
                        z-index: 1;
                        pointer-events: none;
                    }
                }

                .text {
                    padding: 8px;
                    position: relative;
                    border-radius: 12px;
                    border-width: 1px;
                    border-style: solid;
                    background-color: darken(#fff, 1%);
                    white-space: pre-line;
                    word-break: break-word;
                    margin: 2px 0;

                    &:before,
                    &:after {
                        content: '';
                        position: absolute;
                        width: 0;
                        height: 0;
                        border-width: 0;
                        border-style: solid;
                        border-color: transparent;
                    }
                }
            }

            .actions {
                visibility: hidden;
                display: flex;
                align-self: center;

                .el-button {
                    &:first-of-type {
                        margin-left: 10px;
                    }

                    &:last-of-type {
                        margin-right: 10px;
                    }
                }
            }

            &:hover .actions {
                visibility: visible;
            }
        }

        > * {
            &.extra {
                padding: 2px 0;
                color: darken(#fff, 40%);

                .el-button {
                    padding: 0;
                }
            }
        }

        .children {
            width: 100%;
            display: flex;
            flex-direction: column;
            padding: 8px;
            border-radius: 6px;

            > .el-button {
                display: block;
                margin-top: -4px;
                color: darken(#fff, 40%);
            }

            :global(.comments-list) {
                margin-bottom: 4px;
            }
        }

        &.is-reversed {
            flex-direction: row-reverse;

            .container {
                flex-direction: row-reverse;
                margin-right: 8px;

                .el-textarea + small {
                    left: 0;
                    padding-left: 8px;
                }

                .content {
                    &.empty .text {
                        color: lighten(#6AC06F, 16%);
                    }

                    .text {
                        border-color: darken(mix(#fff, #6AC06F, 90%), 5%);
                        background-color: mix(#fff, #6AC06F, 90%);
                        border-bottom-right-radius: 0;

                        &:before,
                        &:after {
                            border-width: 8px 0 0 6px;
                        }

                        &:before {
                            right: -6px;
                            bottom: -1px;
                            border-left-color: darken(mix(#fff, #6AC06F, 90%), 5%);
                        }

                        &:after {
                            right: -4px;
                            bottom: 0;
                            border-left-color: mix(#fff, #6AC06F, 90%);
                        }
                    }
                }
            }

            > *:not(.avatar):not(.container) {
                margin-right: 48px;
            }

            .children {
                align-items: flex-end;

                > .el-button:not(.is-loading):after {
                    content: '—';
                }
            }
        }

        &:not(.is-reversed) {
            .avatar {
                &.el-loading-parent--relative {
                    :global(.el-loading-spinner .circular) {
                        margin-left: -5px;
                    }
                }
            }

            .container {
                margin-left: 8px;

                .content {
                    &.empty .text {
                        color: darken(#fff, 40%);
                    }

                    .text {
                        border-color: darken(#fff, 5%);
                        border-bottom-left-radius: 0;

                        &:before,
                        &:after {
                            border-width: 0 0 8px 6px;
                        }

                        &:before {
                            left: -6px;
                            bottom: -1px;
                            border-bottom-color: darken(#fff, 5%);
                        }

                        &:after {
                            left: -4px;
                            bottom: 0;
                            border-bottom-color: darken(#fff, 1%);
                        }
                    }
                }
            }

            > *:not(.avatar):not(.container) {
                margin-left: 48px;
                padding: 2px 0;
            }

            .children {
                align-items: flex-start;

                > .el-button:not(.is-loading):before {
                    content: '—';
                }
            }
        }
    }
</style>
