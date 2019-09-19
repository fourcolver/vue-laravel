<template>
    <div class="placeholder">
        <img :src="src" :width="size" :style="[(this.$slots.default || this.$slots.secondary) && {marginBottom: '16px'}]" />
        <slot />
        <slot name="secondary" />
    </div>
</template>

<script>
    export default {
        props: {
            src: {
                type: String
            },
            size: {
                type: Number,
                default: 128
            }
        }
    }
</script>

<style lang="scss" scoped>
    .placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        box-sizing: border-box;
        text-align: center;
        img {
            opacity: .64;
            // filter: drop-shadow(4px 4px 8px);
        }
    }
</style>