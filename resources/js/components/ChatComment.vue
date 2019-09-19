<template>
    <div :class="computedClass" class="comment">
        <avatar
            :size="34"
            :src="data.user.avatar"
            :username="data.user.name"
            class="avatar"
        />
        <div class="container">
            <div class="name">
                {{ data.user.name }}
            </div>
            <template v-if="isEditMode">
                <textarea
                    @keydown.alt.enter="nextLine"
                    @keydown.enter.prevent="updateComment"
                    @keydown.esc="cancelEdit"
                    class="content-editable"
                    ref="contentEditable"
                    rows="1"
                    v-if="isEditMode"
                    v-model="content">
                </textarea>
                <small>Press
                    <el-tag size="mini">ESC</el-tag>
                    to
                    <el-button @click="cancelEdit" size="mini" type="text">
                        cancel
                    </el-button>
                </small>
            </template>
            <template v-else>
                <div class="content">{{ content }}</div>
                <el-dropdown
                    @command="handleAction"
                    class="more"
                    size="small"
                    trigger="click"
                    v-if="!data.reversed">
                    <el-button
                        circle
                        icon="el-icon-more"
                        size="mini"
                        type="text"/>
                    <el-dropdown-menu slot="dropdown">
                        <el-dropdown-item command="edit">
                            Edit
                        </el-dropdown-item>
                    </el-dropdown-menu>
                </el-dropdown>
                <small class="time">{{ ago(data.updated_at) }}</small>
            </template>
        </div>
    </div>
</template>

<script>
    import {Avatar} from 'vue-avatar';
    import LoggedInUserMixin from 'mixins/loggedInUserMixin';
    import autosize from 'autosize';
    import {displayError, displaySuccess} from 'helpers/messages';
    import {Loading} from 'element-ui';
    import {distanceInWordsToNow} from 'date-fns';

    export default {
        props: {
            modelId: {
                type: Number,
                required: true
            },
            data: {
                type: Object,
                required: true
            },
            reversed: {
                type: Boolean,
                default: false
            },
            config: {
                type: Object,
                default() {
                    return {
                        update: ''
                    }
                }
            }
        },
        mixins: [LoggedInUserMixin],
        components: {
            Avatar
        },
        data() {
            return {
                isEditMode: false,
                content: this.data.comment
            };
        },
        methods: {
            ago(date) {
                return distanceInWordsToNow(date, {
                    includeSeconds: true,
                    addSuffix: 'ago'
                });
            },
            handleAction(action) {
                switch (action) {
                    case 'edit':
                        this.isEditMode = true;

                        setTimeout(() => {
                            autosize(this.$refs.contentEditable);

                            this.$refs.contentEditable.focus();
                        });

                        break;
                }
            },
            nextLine(ev) {

            },
            async updateComment() {
                let loadingInstance = Loading.service({
                    target: this.$el,
                    customClass: 'loader'
                });

                try {
                    const resp = await this.$store.dispatch(this.config.update, {
                        modelId: this.data.id,
                        postId: this.modelId,
                        comment: this.content
                    });

                    this.cancelEdit();

                    loadingInstance.close();
                } catch (err) {
                    displayError({
                        success: false,
                        message: 'Failed to update comment'
                    });
                    this.cancelEdit();
                    loadingInstance.close();
                }
            },
            cancelEdit() {
                this.isEditMode = false;
                this.content = this.data.comment;
            }
        },
        computed: {
            computedClass() {
                return {
                    'is-reversed': this.reversed,
                    'is-editing': this.isEditMode
                }
            }
        }
    }
</script>


<style lang="scss" scoped>
    .comment {
        display: flex;
        align-items: center;

        :global(.loader) {
            :global(.el-loading-spinner) {
                margin-top: -12px;

                :global(.circular) {
                    width: 24px;
                    height: 24px;
                }
            }
        }

        .avatar {
            align-self: flex-start;
            flex-shrink: 0;
            order: 1;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            order: 2;
            align-items: center;

            .name {
                order: 1;
                width: 100%;
                font-size: 14px;
            }

            .content {
                order: 2;
                background-color: darken(#fff, 4%);
                color: darken(#fff, 80%);
                padding: .5em;
                border-radius: .5em;
                word-break: break-word;
                text-align: left;
            }

            .content-editable {
                order: 2;
                flex: 1;
                resize: none;
                border: 1px darken(#fff, 24%) solid;
                padding: .5em;
                outline: 0;
                font-size: 14px;
                border-radius: .5em;
            }

            .el-dropdown {
                order: 3;

                .el-button {
                    visibility: hidden;
                }
            }

            .time {
                order: 4;
                width: 100%;
                font-size: 10px;
                color: darken(#fff, 56%);
            }

            small {
                order: 4;
            }
        }

        &.is-reversed {
            justify-content: flex-end;
            text-align: right;

            .avatar {
                order: 3;
                margin-left: 1em;
            }

            .container {
                justify-content: flex-end;

                .name {
                    small {
                        order: -1;
                        text-align: left;
                    }
                }

                .content {
                    background-color: mix(#fff, #6AC06F, 80%);
                }

                .el-dropdown {
                    order: 1;
                }

                .time {
                    margin-right: 8px;
                }
            }
        }

        &.is-editing .container {
            flex: 1;
            align-items: stretch;
            flex-direction: column;

            small {
                margin-left: .5em;
            }
        }

        &:not(.is-reversed) .avatar {
            margin-right: 1em;
        }

        &:not(.is-reserved) .container .time {
            margin-left: 8px;
        }

        &:hover .container .el-dropdown .el-button {
            visibility: visible;
        }
    }
</style>
