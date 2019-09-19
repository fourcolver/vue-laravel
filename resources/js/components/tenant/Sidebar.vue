<template>
    <div :class="['sidebar', {'hidden': !visible}]">
        <div ref="menu" class="menu">
            <div :class="['item', {'active': item.active}]" v-for="item in items" :key="item.name" :style="item.style" @click.stop="handleRoute($event, item)">
                <i :class="['icon', item.icon]"></i>
                <div class="title">{{item.title}}</div>
            </div>
        </div>
        <transition-group ref="submenu" tag="div" name="list" :class="['submenu', {'hidden': !submenu.visible}]" :style="{'width': `${submenu.width}px`}">
            <div :class="['item', {'active': item.active}]" v-for="item in submenu.items" :key="item.title" @click.stop="handleRoute($event, item)">
                <i :class="['icon', item.icon]"></i>
                <div class="title">{{item.title}}</div>
                {{item.visible}}
            </div>
        </transition-group>
    </div>
</template>

<script>
    export default {
        props: {
            routes: {
                type: Array,
                default: []
            },
            visible: {
                type: Boolean,
                default: true
            }
        },
        data () {
            return {
                items: [],
                submenu: {
                    width: 0,
                    items: [],
                    visible: false,
                }
            }
        },
        methods: {
            handleRoute ({target}, item) {
                let i, items

                if (item.key.includes('.')) {
                    i = this.submenu.items.length
                    items = this.submenu.items
                } else {
                    i = this.items.length
                    items = this.items
                }

                while (i--) {
                    if (items[i].key !== item.key) {
                        if (items[i].active) {
                            items[i].active = false

                            if (items[i].children) {
                                if (items[i].children.some(({route}) => route && route.name === this.$route.name)) {
                                    let j = items[i].children.length

                                    while (j--) {
                                        if (items[i].children[j].active) {
                                            items[i].children[j].active = false

                                            break
                                        }
                                    }
                                }

                                this.submenu.items = []
                            }

                            break
                        }
                    }
                }

                if (item.children) {
                    item.active = true

                    if (Object.is(item.children, this.submenu.items)) {
                        this.submenu.visible = !this.submenu.visible
                    } else {
                        this.submenu.visible = true
                    }
                    
                    this.submenu.items = item.children

                    if (item.children.every(({route}) => route.name !== this.$route.name)) {
                        item.active = true
                        item.children[0].active = true

                        this.$router.push(item.children[0].route)
                    }
                } else {
                    this.submenu.visible = false

                    if (item.route) {
                        item.active = true

                        this.$router.push(item.route)
                    }
                }
            }
        },
        watch: {
            'visible' (state) {
                if (!state) {
                    this.submenu.visible = false
                }
            },
            'submenu.visible' (state) {
                if (state) {
                    this.submenu.width = document.body.offsetWidth - 112 - 112 / 2
                }
            }
        },
        created () {
            let idx = 0

            this.items = this.routes.reduce((items, item, i) => {
                item.key = `${i}`

                item.active = false

                if (item.children) {
                    if (!this.submenu.items.length) {
                        this.submenu.items = item.children
                    }

                    item.children = item.children.reduce((children, child, childIdx) => {
                        if ('visible' in child && !child.visible) {
                            return children
                        }

                        child.active = false
                        child.key = `${item.key}.${childIdx}`

                        if (child.route) {
                            if (child.route.name === this.$route.name) {
                                item.active = true

                                child.active = true
                            }
                        }

                        children.push(child)

                        return children
                    }, [])
                } else if (item.route && item.route.name === this.$route.name) {
                    item.active = true
                }

                if (item.positionedBottom) {
                    if (!items.find(item => item.positionedBottom)) {
                        item.style = 'margin-top: auto'
                    }

                    items.push(item)
                } else {
                    items.splice(idx, 0, item)

                    idx++
                }

                return items
            }, [])
        },
        mounted () {
            if (this.$el.nextElementSibling) {     
                this.$el.nextElementSibling.style.filter = 'blur(0)'
                this.$el.nextElementSibling.style.transition = 'filter .96s cubic-bezier(.51,.92,.24,1.15)'

                this.$watch(() => this.submenu.visible, state => {
                    if (state) {
                        this.$el.nextElementSibling.style.filter = 'blur(16px)'
                    } else {
                        this.$el.nextElementSibling.style.filter = 'blur(0)'
    
                        this.submenu.visible = false
                    }
                }, {
                    immediate: true
                })

                this.onSiblingElementClickHandler = () => this.submenu.visible = false

                this.$el.nextElementSibling.addEventListener('click', this.onSiblingElementClickHandler)
            }
        },
        beforeDestroy () {
            this.$el.nextElementSibling.removeEventListener('click', this.onSiblingElementClickHandler)
        }
    }
</script>

<style lang="scss" scoped>
    .sidebar {
        height: 100%;
        background-color: #fff;
        transition: margin-left .64s cubic-bezier(.51,.92,.24,1.15);

        &.hidden {
            margin-left: -112px;
        }

        &:not(.hidden) {
            margin-left: 0;
        }

        .menu,
        .submenu {
            height: 100%;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
            overflow-y: overlay;
            overflow-x: hidden;
            scrollbar-width: thin;
            overscroll-behavior: contain;
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
            -webkit-overflow-scrolling: touch;

            &, .item {
                background-color: #fff;
            }

            .item {
                width: 100%;
                cursor: pointer;
                box-sizing: border-box;

                .title {
                    font-size: 14px;
                    overflow: hidden;
                    width: 90%;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                }

                &.active {
                    color: #6AC06F;
                }

                &:hover {
                    background-color: mix(#fff, #6AC06F, 90%);
                }

                &:not(:last-child) {
                    border-bottom: 1px darken(#fff, 8%) solid;
                }
            }

            &::-webkit-scrollbar {
                width: 4px;
            }
 
            &::-webkit-scrollbar-thumb {
                border-radius: 8px;
                width: 4px;
                background-color: transparentize(lighten(#000, 48%), .16);
            }

            &:hover::-webkit-scrollbar-thumb {
                background-color: lighten(#000, 48%);
            }
 
            &::-webkit-scrollbar-track {
                border-radius: 8px;
                background-color: darken(#fff, 2%);
            }

            &::-webkit-scrollbar-thumb:window-inactive {
                background-color: lighten(lighten(#000, 48%), 12%);
            }
        }

        .menu {
            min-width: 112px;
            width: 112px;
            background-color: #fff;
            z-index: 1;

            .item {
                width: 112px;
                height: 112px;
                display: flex;
                flex-shrink: 0;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;

                .icon {
                    font-size: 28px;
                    margin-bottom: 8px;
                }
            }
        }

        .submenu {
            max-width: 320px;
            position: absolute;
            top: 0;
            left: 0;
            z-index: -1;
            will-change: transform;
            transition: transform .64s cubic-bezier(.51, .92, .24, 1.15);

            &.hidden {
                transform: translate3d(-100%, 0, 0);
            }

            &:not(.hidden) {
                transform: translate3d(112px, 0, 0);
            }

            .item {
                padding: 16px;
                display: flex;
                flex-shrink: 0;
                align-items: center;
                opacity: 1;
                position: relative;

                .icon {
                    font-size: 20px;
                    margin-right: 16px;
                }

                &.list-enter-active,
                &.list-leave-active,
                &.list-move {
                    transition: .64s cubic-bezier(0.59, 0.12, 0.34, 0.95);
                    transition-property: opacity, transform;
                }

                &.list-enter {
                    opacity: 0;
                    transform: translateX(50px) scaleY(0.5);
                }

                &.list-enter-to {
                    opacity: 1;
                    transform: translateX(0) scaleY(1);
                }

                &.list-leave-active {
                    position: absolute;
                }

                &.list-leave-to {
                    opacity: 0;
                    transform: scaleY(0);
                    transform-origin: center top;
                }

                &:not(:last-child) {
                    box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                }
            }
        }
    }
</style>