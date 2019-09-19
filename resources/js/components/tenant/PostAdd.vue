<template v-cloak>
    <card :loading="isLoading">
        <avatar
            class="avatar"
            :size="44"
            :src="loggedInUser.avatar"
            :name="loggedInUser.name"
        />
        <autoresize-textarea
            class="content"
            :value="localValue"
            :placeholder="placeholder"
            :maxHeightScroll="maxHeightScroll"
            @input="updateValue"
            @onEnter="onEnter"
        />
        <div class="extra" v-if="hasExtra">
            <slot name="extra" />
        </div>
        <el-button
            round
            size="small"
            type="primary"
            icon="ti-pencil"
            :disabled="isButtonDisabled"
            @click="onEnter">
            Publish
        </el-button>
    </card>
</template>

<script>
    import Card from 'components/Card';
    import Avatar from 'components/Avatar';
    import loggedInUserMixin from 'mixins/loggedInUserMixin';
    import AutoresizeTextarea from 'components/AutoresizeTextarea';

    export default {
        mixins: [loggedInUserMixin],
        props: {
            value: {},
            loading: {
                type: Boolean,
                default: false
            },
            maxHeight: {
                type: Number,
                default: null
            },
            placeholder: {
                type: String,
                default: 'What do you want to publish?'
            }
        },
        components: {
            Card,
            Avatar,
            AutoresizeTextarea
        },
        data () {
            return {
                content: this.value,
                maxHeightScroll: false
            };
        },
        methods: {
            updateValue (newValue) {
                this.localValue = newValue
            },
            onEnter () {
                this.$emit('onEnter');
            }
        },
        computed: {
            localValue: {
                get () {
                    return this.value
                },
                set (newValue) {
                    this.$emit('input', newValue)
                }
            },

            isButtonDisabled () {
                return this.loading || !this.localValue;
            },
            isLoading () {
                return {
                    state: this.loading
                }
            },
            hasExtra () {
                return this.$slots.extra;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        :global(.el-card__body) {
            padding: 0;
            display: flex;
            flex-direction: column;
            .avatar {
                margin: .5em 0 0 1em;
                border-radius: 50%;
                position: absolute;
                box-shadow: 0 1px 3px transparentize(#000, .88),
                            0 1px 2px transparentize(#000, .76);
            }
            .content {
                width: 100%;
                color: lighten(#000, 32%);
                font-size: 1.24em;
                resize: none;
                border-style: none;
                padding: 1em 1em 1em 4em;
                outline: 0;
                box-sizing: border-box;
            }
            .extra {
                background-color: darken(#fff, 1.4%);
                border-top: 1px darken(#fff, 8%) solid;
                border-bottom: 1px darken(#fff, 8%) solid;
                padding: 1em;
            }
            .el-button {
                margin: 1em;
                :global(i + span) {
                    margin-left: 5px;
                }
            }
        }
    }
</style>
