<script>
    import {mapActions} from 'vuex';

    export default {
        data() {
            return {}
        },
        created() {
            const token = this.$route.query.token;

            if (!token) {
                this.$router.push({name: 'login'});
                return;
            }

            this.autoLogin({token}).then((response) => {
                window.location.href = response.redirect;
            }).catch((error) => {
                this.$router.push({name: 'login'});
            });
        },
        render() {
            return false;
        },
        methods: {
            ...mapActions(['autoLogin'])
        }
    }
</script>
