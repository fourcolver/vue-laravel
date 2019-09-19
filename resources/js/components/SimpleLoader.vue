<template>
    <div class="simple-loader" :class="computedClass" v-if="visible">
        <i class="el-icon-loading" :style="computedStyle"></i>
    </div>
</template>

<script>
    export default {
        props: {
            visible: {
                type: Boolean,
                default: false
            },
            centered: {
                type: Boolean,
                default: false
            },
            size: {
                type: Number,
                default: 16
            },
        },
        computed: {
            computedClass () {
                return {
                    centered: this.centered
                };
            },
            computedStyle () {
                return {
                    fontSize: this.size + 'px'
                };
            }
        }
    }
</script>

<style lang="scss" scoped>
    .simple-loader {
        &:not(.centered) {
            display: inline-flex;
        }
        &.centered {
            display: flex;
            justify-content: center;
        }
    }
</style>

