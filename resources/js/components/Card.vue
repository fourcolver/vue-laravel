<template>
    <el-card v-bind="$attrs"
             v-on="$listeners"
             v-loading="loading.state"
             :element-loading-text="loading.text"
             :element-loading-spinner="loading.icon"
             :element-loading-background="loading.background">
        <template slot="header" v-if="$slots.header">
            <slot name="header"></slot>
        </template>
        <slot />
    </el-card>
</template>

<script>
    export default {
        inheritAttrs: false,
        props: {
            loading: {
                type: Object,
                default: () => ({
                    state: false,
                    text: 'Loading...',
                    icon: 'el-icon-loading',
                    background: 'rgba(0, 0, 0, 0.8)'
                })
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        position: relative;
        border-radius: 6px;
        display: flex;
        flex-direction: column;
        &.is-always-shadow {
            box-shadow: 0 1px 3px transparentize(#000, .88),
                        0 1px 2px transparentize(#000, .76);
        }
        :global(.el-card__header) {
            padding: 16px;
            font-size: 20px;
            border-style: none;
            display: flex;
            align-items: center;
        }
        :global(.el-card__body) {
            padding: 16px;
            &:after {
                content: '';
                display: table;
                clear: both;
            }
        }
    }
</style>
