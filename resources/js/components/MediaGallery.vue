<template>
    <div :id="id" class="blueimp-gallery blueimp-gallery-controls">
        <div class="slides"></div>
        <a class="prev">
            <i class="el-icon-arrow-left"></i>
        </a>
        <a class="next">
            <i class="el-icon-arrow-right"></i>
        </a>
        <a class="close">
            <i class="el-icon-close"></i>
        </a>
    </div>
</template>

<script>
    import Gallery from 'vue-gallery'
    import uuid from 'uuid/v1'

    export default {
        extends: Gallery,
        components: {
            Gallery
        },
        computed: {
            id () {
                if (this.$attrs.id) {
                    return this.$attrs.id
                }

                return `gallery-${this.$_uid}`
            }
        },
        beforeCreate () {
            this.$delete(this.$options.props, 'id')
            this.$delete(this.$options.props, 'carousel')
        }
    }
</script>

<style lang="scss">
    .blueimp-gallery.blueimp-gallery-controls {
        > .next,
        > .prev {
            display: flex !important;
            align-items: center;
            justify-content: center;
            background: #6AC06F;
            border-color: #6AC06F;
            opacity: 1;
            
            i {
                font-size: 24px;
            }
        }
    }
</style>