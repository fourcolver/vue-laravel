import {mapGetters, mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import unitTypes from 'mixins/methods/unitTypes';

export default (config = {}) => {
    let mixin = {
        mixins: [unitTypes],
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                remoteLoading: false,
                tenants: [],
                buildings: [],
                model: {
                    tenant_id: '',
                    name: '',
                    type: 1,
                    room_no: '',
                    monthly_rent: '',
                    floor: '',
                    sq_meter: '',
                    basement: false,
                    attic: false,
                    building_id: this.$route.params.id,
                    selected_tenant: ''
                },
                validationRules: {
                    tenant_id: [{
                        required: false,
                        message: 'This field is required'
                    }],
                    name: [{
                        required: true,
                        message: this.$t("models.unit.validation.name.required")
                    }],
                    type: [{
                        required: true
                    }],
                    room_no: [{
                        required: true,
                        message: this.$t("models.unit.validation.room_no.required")
                    }],
                    monthly_rent: [{
                        required: true,
                        message: this.$t("models.unit.validation.monthly_rent.required")
                    }],
                    floor: [{
                        required: true,
                        message: this.$t("models.unit.validation.floor.required")
                    }],
                    building_id: [{
                        required: true,
                        message: this.$t("models.unit.validation.building.required")
                    }]
                },
                loading: {
                    state: false,
                    text: 'Please wait...'
                },
                requestColumns: [{
                    prop: 'category.name',
                    label: this.$t('models.request.category')
                }],
                requestActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.request.edit'),
                        type: 'primary',
                        onClick: this.requestEditView
                    }]
                }],
            }
        },
        methods: {
            ...mapActions(['getTenants', 'getBuildings']),
            requestEditView(row) {
                return this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                });
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.buildings = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getBuildings({get_all: true, search});

                        this.buildings = data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async remoteSearchTenants(search) {
                if (search === '') {
                    this.tenants = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getTenants({get_all: true, search});

                        this.tenants = data;
                        this.tenants.forEach(t => t.name = `${t.first_name} ${t.last_name}`);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            }
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            rooms() {
                let rooms = [];

                for (let i = 1; i <= 6.5; i += .5) {
                    rooms.push({
                        value: i,
                        label: i
                    });
                }

                return rooms;
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    async submit() {
                        const valid = await this.form.validate();
                        if (valid) {
                            this.loading.state = true;
                            try {
                                const response = await this.createUnit(this.model);
                                displaySuccess(response);

                                this.form.resetFields();
                                return response;
                            } catch (err) {
                                displayError(err);
                            } finally {
                                this.loading.state = false;
                            }
                        }
                    },

                    ...mixin.methods,
                    ...mapActions(['createUnit'])
                };

                break;
            case 'edit':
                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }

                                this.loading.state = true;
                                try {
                                    displaySuccess(await this.updateUnit(this.model));
                                    resolve(true);
                                } catch (err) {
                                    displayError(err);
                                    resolve(false);
                                } finally {
                                    this.loading.state = false;
                                }
                            });
                        });
                    },
                    ...mixin.methods,
                    ...mapActions(['getUnit', 'updateUnit', 'getBuilding'])
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;

                        this.model = await this.getUnit({id: this.$route.params.id});

                        if (this.model.tenant) {
                            this.$set(this.model, 'tenant_id', this.model.tenant.id);
                            this.remoteSearchTenants(`${this.model.tenant.first_name}`);
                        }

                        if (config.withRelation && this.model.building_id) {
                            const building = await this.getBuilding({id: this.model.building_id});
                            this.remoteSearchBuildings(`${building.name}`);
                        }

                    } catch (err) {
                        this.$router.replace({
                            name: 'adminUnits'
                        });

                        displayError(err);
                    } finally {
                        this.loading.state = false;
                    }
                };

                break;
        }
    }

    return mixin;
};
