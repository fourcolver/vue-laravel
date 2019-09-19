import {mapActions} from "vuex";

export default {
    methods: {
        ...mapActions(['getPropertyManagers']),
        async getFilterPropertyManagers() {
            this.loading = true;
            const propertyManagers = await this.getPropertyManagers({
                get_all: true
            });
            this.loading = false;

            let data = this.managersMapper(propertyManagers.data);

            return data;
        },
        managersMapper(propertyManagers) {
            return propertyManagers.map((propertyManager) => {
                return {
                    id: propertyManager.id,
                    name: propertyManager.user.name
                }
            })
        }
    }
}
