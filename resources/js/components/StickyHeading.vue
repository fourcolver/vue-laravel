<template>
    <el-card class="sticky-heading" v-sticky="{stickyTop, zIndex: stickyZindex, disabled: stickyDisabled}">
        <div ref="sentinel" class="sentinel" v-if="!sentinel"></div>
        <heading :icon="icon" :title="title">
            <template #description>
                <slot name="description" />
            </template>
            <slot />
        </heading>
    </el-card>
</template>

<script>
    import Heading from 'components/Heading'
    import VueSticky from 'vue-sticky'


    export default {
        props: {
            icon: String,
            title: {
                type: String,
                required: true
            },
            stickyZindex: {
                type: Number,
                default: 1
            },
            stickyTop: {
                type: Number,
                default: 0
            },
            stickyDisabled: {
                type: Boolean,
                default: false
            },
            sentinel: {
                type: String,
                validator: value => value[0] === '#' && value.length > 1
            }
        },
        directives: {
            sticky: VueSticky
        },
        components: {
            Heading
        },
        data () {
            return {
                $observer: null
            }
        },
        mounted () {
            this.$observer = new IntersectionObserver(entries => {
                if (!entries[0].isIntersecting) {
                    this.$emit('on-stick', true)
                } else {
                    this.$emit('on-stick', false)
                }
            })

            this.$observer.observe(this.sentinel ? document.getElementById(this.sentinel.slice(1)) : this.$refs.sentinel)
        },
        beforeDestroy () {
            this.$observer.disconnect()
        }
    }
</script>

<style lang="scss" scoped>
    .el-card.sticky-heading {
        :global(.el-card__body) {
            padding: 12px 16px;
    
            .sentinel {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 1px;
                visibility: hidden;
                pointer-events: none;
            }
        }
    }
</style>