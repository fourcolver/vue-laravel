<template>
    <div :class="['add-comment', {'with-templates': showTemplates}]">
        <el-tooltip :content="user.name" effect="dark" placement="top-start">
            <avatar :name="user.name" :size="32" :src="user.avatar" />
        </el-tooltip>
        <div class="content">
            <el-input autosize ref="content" :class="{'is-focused': focused}" type="textarea" resize="none" v-model="content" :placeholder="$t('components.common.addComment.placeholder')" :disabled="loading" :validate-event="false" @blur="focused = false" @focus="focused = true" @keydown.native.alt.enter.exact="save" />
            <el-dropdown class="templates" size="small" placement="top-end" trigger="click" @command="onTemplateSelected" @visible-change="onDropdownVisibility" v-if="showTemplates">
                <el-tooltip ref="templates-button-tooltip" :content="$t('components.common.addComment.tooltipTemplates')" placement="top-end">
                    <el-button ref="templates-button" type="text" class="el-dropdown-link" :disabled="loading">
                        <i class="icon-ellipsis-vert"></i>
                    </el-button>
                </el-tooltip>
                <el-dropdown-menu slot="dropdown">
                    <el-dropdown-item v-for="(template, idx) in templates" :key="template.id" :command="template" :divided="!!idx">
                        {{template.name}}
                        <small style="display: block;color: #A9A9A9;width: 280px;white-space: nowrap;overflow: hidden;text-overflow: ellipsis;">{{template.subject}}</small>
                    </el-dropdown-item>
                    <el-dropdown-item disabled v-if="loadingTemplates">
                        {{$t('components.common.addComment.loadingTemplates')}}
                    </el-dropdown-item>
                    <el-dropdown-item disabled v-else-if="!loadingTemplates && !templates.length">
                        {{$t('components.common.addComment.emptyTemplatesPlaceholder')}}
                    </el-dropdown-item>
                </el-dropdown-menu>
            </el-dropdown>
        </div>
        <el-tooltip :content="$t('components.common.addComment.saveShortcut', {shortcut: saveKeysShortcut})" placement="top-end">
            <el-button circle icon="icon-paper-plane" size="small" :disabled="!content" :loading="loading" @click="save" />
        </el-tooltip>
    </div>
</template>

<script>
    import Avatar from './Avatar'
    import {mapActions, mapGetters} from 'vuex'
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        props: {
            id: {
                type: Number,
                required: true
            },
            parentId: {
                type: Number,
            },
            type: {
                type: String,
                required: true,
                validator: type => ['post', 'product', 'request', 'conversation'].includes(type)
            },
            autofocus: {
                type: Boolean,
                default: false
            },
            reversed: {
                type: Boolean,
                default: false
            },
            showTemplates: {
                type: Boolean,
                default: false
            }
        },
        components: {
            Avatar
        },
        data() {
            return {
                content: '',
                focused: false,
                loading: false,
                loadingTemplates: false,
                dropdownTemplatesVisible: false
            }
        },
        methods: {
            ...mapActions({
                getTemplates: 'getRequestTemplates'
            }),

            focus () {
                this.$refs.content.focus()
            },
            onTemplateSelected (template) {
                const caretPosition = this.$refs.content.$el.querySelector('textarea').selectionStart

                this.content = this.content.substring(0, caretPosition) + template.subject + this.content.substring(caretPosition)

                this.focus()
            },
            onDropdownVisibility (state) {
                this.dropdownTemplatesVisible = state

                this.$refs['templates-button-tooltip'].hide()
            },
            async save () {
                if (!/\S/.test(this.content)) {
                    return
                }

                try {
                    this.loading = true
                    this.$refs.content.blur()

                    await this.$store.dispatch('comments/create', {
                        id: this.id,
                        comment: this.content,
                        commentable: this.type,
                        parent_id: this.parentId
                    });

                } catch (error) {
                    displayError(error)
                } finally {
                    this.focus()

                    this.content = ''
                    this.loading = false
                }
            }
        },
        computed: {
            ...mapGetters({
                user: 'loggedInUser',
                templatesWithId: 'getRequestTemplatesWithId'
            }),

            templates () {
                return this.templatesWithId(this.id) || []
            },
            canShowTemplates () {
                return this.showTemplates && ['request'].includes(this.type)
            },
            saveKeysShortcut () {
                if (navigator.platform.toUpperCase().includes('MAC')) {
                    return 'option+enter'
                }

                return 'alt+enter'
            }
        },
        async mounted () {
            if (this.canShowTemplates) {
                if (!this.templates.length) {
                    try {
                        this.loadingTemplates = true

                        await this.getTemplates({id: this.id})
                    } catch (error) {
                        displayError(error)
                    } finally {
                        this.loadingTemplates = false

                        if (this.dropdownTemplatesVisible) {
                            this.$refs['templates-button'].$el.click()
                        }
                    }
                }
            }

            if (this.autofocus) {
                this.focus()
            }
        }
    }
</script>

<style lang="scss" scoped>
    .add-comment {
        width: 100%;
        display: flex;
        align-items: flex-end;
        font-size: 14px;
        position: relative;

        .content {
            position: relative;
            flex: auto;

            .el-textarea {
                position: relative;
                margin-left: 8px;

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

                & + .el-dropdown {
                    position: absolute;
                    bottom: 0;
                    right: -2px;

                    .el-dropdown-link {
                        padding: 9px;
                    }
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

            & + .el-button {
                margin-left: 16px;
            }
        }

        &.with-templates {
            .content {
                .el-textarea {
                    :global(.el-textarea__inner) {
                        padding-right: 32px;
                    }
                }
            }
        }
    }
</style>
