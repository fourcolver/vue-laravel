<template>
    <div :class="['heading', `${shadow}-shadow`]">
        <i :class="['icon', icon]" v-if="icon"></i>
        <div class="content">
            <div class="title">{{title}}</div>
            <slot name="description" />
        </div>
        <slot />
    </div>
</template>

<script>
    export default {
        props: {
            icon: String,
            title: {
                type: String,
                required: true
            },
            shadow: {
                type: String,
                default: 'light',
                validator: type => ['light', 'heavy'].includes(type)
            },
            description: String
        }
    }
</script>

<style lang="scss" scoped>
    .heading {
        display: flex;
        align-items: center;
        flex-shrink: 0;
        position: relative;
        z-index: 1;

        &.light-shadow .icon {
            box-shadow: 0 1px 3px transparentize(#000, .88),
                        0 1px 2px transparentize(#000, .76);
        }

        &.heavy-shadow .icon {
            box-shadow: 0 0.46875rem 2.1875rem rgba(4,9,20,.03),
                        0 0.9375rem 1.40625rem rgba(4,9,20,.03),
                        0 0.25rem 0.53125rem rgba(4,9,20,.05),
                        0 0.125rem 0.1875rem rgba(4,9,20,.03);
        }

        .icon {
            width: 56px;
            height: 56px;
            background-color: transparentize(#fff, .2);
            color: var(--primary-color);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 22px;
            margin-right: 16px;
        }

        .content {
            flex: auto;
            min-width: 0;
            flex-shrink: 0;

            .title {
                color: var(--primary-color);
                font-size: 24px;
                font-weight: bold;
                overflow: hidden;
                min-width: 0;
                text-overflow: ellipsis;
                white-space: nowrap;
            }
        }
    }
</style>
