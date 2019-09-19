import {mapActions} from "vuex";

export default {
    methods: {
        ...mapActions(['getDistricts']),
        async getFilterDistricts() {
            this.loading = true;
            const districts = await this.getDistricts({
                get_all: true
            });
            this.loading = false;

            return districts.data;
        }
    }
}
