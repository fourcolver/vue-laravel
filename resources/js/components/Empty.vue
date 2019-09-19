<template>
    <div class="empty" :class="{inverted}">
        <img :src="image" :width="size" />
        {{ text }}
        <small v-if="hasSlot">
            <slot />
        </small>
    </div>
</template>

<script>
    export default {
        props: {
            image: {
                type: String,
                required: true
            },
            text: {
                type: String
            },
            size: {
                type: Number,
                default: 128
            },
            inverted: {
                type: Boolean,
                default: false
            }
        },
        methods: {
            hasSlot () {
                return !!this.$slots.default;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .empty {
        padding: 1em;
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        font-size: 16px;
        line-height: 1.56;
        box-sizing: border-box;
        img {
            margin-bottom: 1em;
            opacity: .64;
        }
        small {
            color: darken(#fff, 28%);
            font-size: 72%;
        }
        &.inverted {
            color: darken(#fff, 18%);
            img {
                filter: saturate(0) brightness(4) drop-shadow(0 0 16px);
            }
        }
    }
</style>