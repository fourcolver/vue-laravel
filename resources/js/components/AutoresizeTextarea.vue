<template>
    <textarea
        ref="textarea"
        :value="value"
        :style="computedStyles"
        v-bind="$attrs"
        v-on="listeners"
        @focus="autoresize"
        @keydown.alt.enter="nextLine"
        @keydown.enter.prevent="onEnter"
    ></textarea>
</template>

<script>
    export default {
        name: 'AutoresizeTextarea',
        // inheritAttrs: false,
        props: {
            value: {
                type: String,
                default: ''
            },
            maxHeight: {
                type: Number,
                default: null
            }
        },
        data () {
            return {
                maxHeightScroll: false
            };
        },
        methods: {
            autoresize () {
                this.$refs.textarea.style.setProperty('height', 'auto');

                let contentHeight = this.$refs.textarea.scrollHeight + 1;

                if (this.maxHeight) {
                    if (contentHeight > this.maxHeight) {
                        contentHeight = this.maxHeight;
                        this.maxHeightScroll = true;
                    } else {
                        this.maxHeightScroll = false;
                    }
                }

                this.$refs.textarea.style.setProperty('height', contentHeight + 'px');
            },
            nextLine (ev) {
                ev.target.value += '\r\n';

                this.updateValue(ev)
            },
            updateValue (ev) {
                this.$emit('input', ev.target.value);
            },
            onEnter (ev) {
                this.$emit('onEnter', ev);
            }
        },
        computed: {
            listeners () {
                return {
                    ...this.$listeners,
                    input: this.updateValue
                };
            },
            computedStyles () {
                let styles = {
                    resize: 'none'
                };

                if (!this.maxHeightScroll) {
                    styles.overflow = 'hidden';
                }

                return styles;
            }
        },
        watch: {
            value () {
                this.$nextTick(this.autoresize);
            }
        },
        mounted () {
            this.autoresize();
        }
    };
</script>
