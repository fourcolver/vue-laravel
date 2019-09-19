<template>
    <div class="drawer-wrapper">
        <div class="drawer" :style="drawerStyle">
            <slot />
        </div>
        <div class="drawer-container" :style="containerStyle">
            <div class="drawer-overlay" :style="overlayStyle" @click="toggle" />
            <slot name="content" />
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            size: {
                type: Number,
                default: 384
            },
            docked: {
                type: Boolean,
                default: false
            },
            visible: {
                type: Boolean,
                default: false
            },
            position: {
                type: String,
                default: 'right',
                validator: value => ['top', 'right', 'bottom', 'left'].includes(value)
            }
        },
        methods: {
            toggle () {
                this.$emit('update:visible', !this.visible)
            }
        },
        computed: {
            drawerStyle () {
                let styling = {
                    top: {
                        width: '100%',
                        height: `${this.size}px`,
                        top: 0,
                        left: 0,
                        transform: `translate3d(0, ${this.visible ? 0 : `-${this.size}px`}, 0)`
                    },
                    right: {
                        width: `${this.size}px`,
                        top: 0,
                        right: `-${this.size}px`,
                        bottom: 0,
                        transform: `translate3d(${this.visible ? `-${this.size}px` : 0}, 0, 0)`
                    },
                    bottom: {
                        width: '100%',
                        height: `${this.size}px`,
                        left: 0,
                        bottom: 0,
                        transform: `translate3d(0, ${this.visible ? 0 : `${this.size}px`}, 0)`
                    },
                    left: {
                        width: `${this.size}px`,
                        top: 0,
                        left: `-${this.size}px`,
                        bottom: 0,
                        transform: `translate3d(${this.visible ? `${this.size}px` : 0}, 0, 0)`
                    }
                };

                return styling[this.position] || styling['right'];
            },
            containerStyle () {
                if (!this.docked) {
                    return
                }

                let styling = {
                    top: `translateY(${this.visible ? `${this.size}px` : 0})`,
                    right: `translateX(${this.visible ? `-${this.size}px` : 0})`,
                    bottom: `translateY(${this.visible ? `-${this.size}px` : 0})`,
                    left: `translateX(${this.visible ? `${this.size}px` : 0})`,
                };

                return {
                    transform: styling[this.position] || styling['right']
                }
            },
            overlayStyle () {
                return {
                    opacity: this.visible ? 1 : 0,
                    visibility: this.visible ? 'visible' : 'hidden'
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .drawer-wrapper {
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        overflow: hidden;
        .drawer {
            background-color: #fff;
            box-shadow: 0 16px 28px 0 transparentize(#000, .78), 
                        0 25px 55px 0 transparentize(#000, .79);
            position: absolute;
            z-index: 9;
            transition: transform .48s;
            will-change: transform;
        }
        .drawer-container {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1;
            overflow: hidden;
            transform: translateZ(0);
            transition: transform .6s cubic-bezier(.51,.92,.24,1.15);
            will-change: transform;
            .drawer-overlay {
                z-index: 99;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                transition: opacity .36s ease-in-out;
                background: transparentize(#000, .44);
            }
        }
    }
</style>