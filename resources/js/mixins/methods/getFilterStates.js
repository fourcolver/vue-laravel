import {mapActions} from "vuex";

export default {
    methods: {
        ...mapActions(['getStates']),
        async getFilterStates() {
            this.loading = true;
            const states = await this.getStates({
                filters: true
            });
            this.loading = false;

            return states.data;
        }
    }
}