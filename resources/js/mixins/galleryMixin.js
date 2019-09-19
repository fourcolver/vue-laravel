import Gallery from 'vue-gallery';

export default {
    props: {
        data: {
            type: Array,
            required: true
        },
        height: {
            type: Number,
            default: 300
        }
    },
    data () {
        return {
            image: null,
            options: {}
        };
    },
    components: {
        Gallery
    },
    methods: {
        open (image) {
            this.image = image;
        },
        close () {
            this.image = null;
        }
    },
    computed: {
        images () {
            return this.data.map(({url}) => url);
        }
    }
}
