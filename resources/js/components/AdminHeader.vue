<template>
    <el-menu background-color="#fff" mode="horizontal">
        <el-menu-item index="prefix" v-if="hasSlot('prefix')">
            <slot name="prefix"></slot>
        </el-menu-item>
        <el-menu-item class="logo" index="logo">
            <img src="~img/logo3.png"/>
        </el-menu-item>
        <el-menu-item class="header-menu-links" index="content" v-if="hasSlot()">
            <slot/>
        </el-menu-item>
    </el-menu>
</template>

<script>
    export default {
        name: 'AdminHeader',
        methods: {
            hasSlot(slot) {
                if (slot) {
                    return !!this.$slots[slot];
                }

                return !!this.$slots.default;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-menu {
        &.el-menu--horizontal {
            z-index: 10;
            box-shadow: 0 1px 5px 5px transparentize(#000, .9);
            display: flex;
            align-items: center;
            flex-shrink: 0;
            padding: 0 1em;

            > .el-menu-item {
                height: 72px;
                line-height: 40px;
                padding: 0 1em;
                background: none !important;
                border-bottom-style: none !important;

                :global(.el-button i) {
                    color: inherit;
                }

                &.logo {
                    flex: 1;
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

<style lang="scss">
    .el-menu {
        &.el-menu--horizontal {

            > .el-menu-item.header-menu-links {
                display: flex !important;
                align-items: center;
                width: auto;

                font-size: 15px;

                .header-link{
                    line-height: 1;
                    text-align: center;
                    text-decoration: none;
                    color: #909399;

                    i {
                        margin-bottom: 5px;
                        margin-right: 10px;
                    }

                    &:hover {
                        color: #6AC06F;

                        i {
                            color: #6AC06F;
                        }
                    }
                }

                .dropdown-list-item-link.active{
                    background-color: #f0f9f1;
                    padding: 10px 100% 10px 10px;
                    margin-left: -10px;
                }

                .header-link.active{
                    color: #6AC06F;
                }
            }
        }
    }

    .el-menu.el-menu--horizontal{
        border-bottom: none !important;
    }

    .el-menu-item{

        &:hover {
            background-color: #f0f9f1!important;
        }

        &-link{
            line-height: 1;
            text-align: center;
            text-decoration: none;
            color: #909399;

            i {
                margin-bottom: 5px;
                margin-right: 10px;
            }
        }

        .logout-button {
            display: flex;
            color: #909399;

            i {
                margin-bottom: 5px;
                margin-right: 14px;
            }

            &:hover {
                color: #6AC06F;
            }
        }
    }

    .el-menu-item-d.is-active{
        background-color: #f0f9f1!important;
    }

    .dropdown-menu .el-submenu{
        .el-submenu__title{
            border-bottom: none !important;

            .el-submenu__icon-arrow {
                display: none !important;
            }

            .user-params {
                .el-submenu__icon-arrow{
                    display: block !important;
                }
            }
        }

    }

    .el-menu--popup {
        top: 13px;
        left: -16px;
        min-width: 153px !important;
    }
</style>
