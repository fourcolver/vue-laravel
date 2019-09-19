<template>
    <el-menu mode="horizontal" background-color="#fff">
        <slot name="prefix" />
        <el-menu-item index="logo" class="logo">
            <img src="~img/logo3.png" />
        </el-menu-item>
        <slot />
    </el-menu>
</template>

<style lang="scss" scoped>
    .el-menu {
        z-index: 999;
        &.el-menu--horizontal {
            box-shadow: 0 1px 3px transparentize(#000, .88),
                        0 1px 2px transparentize(#000, .76);
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding: 0 1em;
            > .el-menu-item {
                height: 72px;
                line-height: 72px;
                padding: 0 1em;
                background: none !important;
                border-bottom-style: none !important;
                :global(.el-button i) {
                    color: inherit;
                }
                &.logo {
                    flex: auto;
                    padding: 1em;
                    img {
                        height: 100%;
                        vertical-align: baseline;
                    }
                }
            }
        }
    }
</style>
