<template>
    <div class="avatar" :style="style">
        <span v-if="!src" style="pointer-events: none">{{ initials }}</span>
    </div>
</template>

<script>
    export default {
        props: {
            name: {
                type: String,
                default: ''
            },
            src: {
                type: String
            },
            size: {
                type: Number,
                default: 40
            }
        },
        methods: {
            color (str) {
                let hash = 0;

                for (let i = 0, length = str.length; i < length; i++) {
                    hash = str.charCodeAt(i) + ((hash << 5) - hash)
                }

                let color = '#';

                for (let i = 0; i < 3; i++) {
                    const value = (hash >> (i * 8)) & 0xFF

                    color += ('00' + value.toString(16)).substr(-2)
                }

                return color;
            },
            lightenColor (hex, amount) {
                let usePound = false

                if (hex[0] === '#') {
                    hex = hex.slice(1)
                    usePound = true
                }

                let num = parseInt(hex, 16)
                let r = (num >> 16) + amount

                if (r > 255) r = 255
                else if (r < 0) r = 0

                let b = ((num >> 8) & 0x00FF) + amount

                if (b > 255) b = 255
                else if (b < 0) b = 0

                let g = (num & 0x0000FF) + amount

                if (g > 255) g = 255
                else if (g < 0) g = 0
                
                return (usePound ? '#' : '') + (g | (b << 8) | (r << 16)).toString(16)
            }
        },
        computed: {
            style () {
                let style = {
                    display: 'inline-flex',
                    alignItems: 'center',
                    justifyContent: 'center',
                    borderRadius: '50%',
                    flexShrink: '0',
                    width: `${this.size}px`,
                    height: `${this.size}px`,
                    verticalAlign: 'middle'
                }

                if (this.src) {
                    style.backgroundImage = `url('/${this.src}')`
                    style.backgroundRepeat = 'no-repeat'
                    style.backgroundSize =  'cover'
                    style.backgroundPosition = 'center center'
                } else {
                    style.backgroundColor = this.color(this.name)
                    style.fontSize = Math.floor(this.size / 2.5) + 'px'
                    style.color = this.lightenColor(style.backgroundColor, 80)
                }

                return style
            },
            initials () {
                let parts = this.name.split(/[ -]/)
                let initials = ''

                for (let i = 0, length = parts.length; i < length; i++) {
                    initials += parts[i].charAt(0)
                }

                if (initials.length > 3 && initials.search(/[A-Z]/) !== -1) {
                    initials = initials.replace(/[a-z]+/g, '')
                }

                initials = initials.substr(0, 3).toUpperCase()

                return initials
            }
        }
    }
</script>