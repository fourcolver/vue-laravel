<template>


    <aside class="el-menu-aside">
        <el-menu :default-active="currActive" :unique-opened="true">
            <li class="slot" index="slot" v-if="hasSlot">
                <slot/>
            </li>

            <ul class="content">
                <el-menu-item
                        :class="{nested: link.nestedItem }"
                        :index="link.title"
                        :key="link.title"
                        @click="handleLink($event, key, link)"
                        v-for="(link, key) in links"
                        v-if="!link.children && ($can(link.permission) || !link.permission)">
                    <router-link :to="{name: link.route.name}">
                        <i :class="[link.icon, 'icon']"/>
                        <span class="title">{{ link.title }}</span>
                    </router-link>
                </el-menu-item>
                <el-submenu :index="link.title" v-else-if="($can(link.permission) || !link.permission)">
                    <template slot="title">
                        <i :class="[link.icon, 'icon']"/>
                        <span class="title">{{ link.title }}</span>
                    </template>
                    <el-menu-item
                            :index="child.title"
                            :key="child.title"
                            @click="handleLink($event, childKey, child)"
                            v-for="(child, childKey) in link.children">

                        <router-link :to="child.route">
                            <i :class="['icon-right-1', 'icon']"/>
                            <span class="title">{{ child.title }}</span>
                        </router-link>
                    </el-menu-item>
                </el-submenu>
            </ul>
        </el-menu>
    </aside>
</template>

<script>
    import {displaySuccess, displayError} from 'helpers/messages';

    export default {
        name: 'AdminSidebar',
        props: {
            links: {
                type: Array,
                default: []
            },
            defaultActive: {
                default: '0'
            }
        },
        data() {
            return {
                currActive: this.defaultActive
            }
        },
        methods: {
            handleLink(ev, key, {route, action, children, icon}) {
                //this.currActive = key.toString();

                !children && route && this.$router.push(route);

                /*if (!children && !!icon) {
                    console.log('el', this.$el);
                    const element1 = document.body.querySelector('.el-submenu.is-opened');
                    console.log('element', element1);
                    if (element1) {
                        element1.classList.remove('is-opened');
                        element1.removeAttribute('aria-expanded');
                        element1.querySelector('ul').style.display = 'none';
                    }
                }*/

                if (action) {
                    if (action.showConfirmation) {
                        action && this.$confirm('Please proceed with caution.', 'Are you sure?', {
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel',
                            type: 'warning',
                            roundButton: true
                        }).then(() => {
                            this.$store.dispatch(action.name)
                                .then(r => displaySuccess(r))
                                .catch(err => displayError(err));
                        }).catch(() => {
                        });
                    } else {
                        this.$store.dispatch(action.name)
                            .then(r => displaySuccess(r))
                            .catch(err => displayError(err));
                    }
                }
            }
        },
        computed: {
            hasSlot() {
                return !!this.$slots.default;
            }
        },
        created() {
            const routeName = this.$route.name;
            
            this.links.map(link => {
                if (link.route && link.route.name == routeName) {
                    this.currActive = link.title;
                }
                else if (link.children) {
                    let dActive = '';
                    link.children.map(child => {
                        if (child.route &&  child.route.name == routeName) {
                            this.currActive = child.title;
                        }
                    });
                }
            });
        }
    }
</script>

<style lang="scss" scoped>
    .el-menu {
        width: 256px;
        display: flex;
        flex-direction: column;
        border-right: none !important;

        &-aside{
            background: #fff;
        }

        .content {
            padding: 0;
            // overflow: auto;

            a {
                color: #303133;
                text-decoration: none;
            }

            .is-active:not(.el-submenu) {
                background-color: #f0f9f1;
                > a {
                    font-weight: bold;
                }
            }

            .el-menu-item,
            :global(.el-submenu__title) {
                .icon {
                    vertical-align: middle;
                    margin-right: 5px;
                    width: 24px;
                    text-align: center;
                    font-size: 18px;
                }

                &.nested {
                    padding: 0 40px !important;
                }
            }
        }
    }

    .el-submenu :global(.el-menu) {
        background-color: darken(#fff, 2.4%);
    }
</style>
