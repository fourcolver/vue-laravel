import {mapGetters, mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import ServicesTypes from 'mixins/methods/servicesTypes';

export default (config = {}) => {
    let mixin = {
        mixins: [ServicesTypes],
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                remoteLoading: false,
                statistics: {
                    raw: [{
                        icon: 'ti-user',
                        color: '#003171',
                        value: 0,
                        description: 'Tenants'
                    }, {
                        icon: 'ti-plus',
                        color: '#26A65B',
                        value: 0,
                        description: 'Total units'
                    }],
                    percentage: {
                        occupied_units: 0,
                        free_units: 0,
                    }
                },
                model: {
                    name: '',
                    description: '',
                    floor_nr: 1,
                    state_id: '',
                    city: '',
                    street: '',
                    street_nr: '',
                    zip: '',
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: this.$t("models.building.validation.name.required")
                    }],
                    // description: [{
                    //     required: true,
                    //     message: this.$t("models.building.validation.description.required")
                    // }],
                    floor_nr: [{
                        required: true,
                        message: this.$t("models.building.validation.floor_nr.required")
                    }],
                    state_id: [{
                        required: true,
                        message: this.$t('models.address.validation.state.required')
                    }],
                    city: [{
                        required: true,
                        message: this.$t('models.address.validation.city.required')
                    }],
                    street: [{
                        required: true,
                        message: this.$t('models.address.validation.street.required')
                    }],
                    street_nr: [{
                        required: true,
                        message: this.$t('models.address.validation.street_nr.required')
                    }],
                    zip: [{
                        required: true,
                        message: this.$t('models.address.validation.zip.required')
                    }],
                },
                loading: {
                    state: false,
                    text: 'Please wait...'
                },
                allServices: [],
                districts: []
            };
        },
        methods: {
            ...mapActions(['getStates', 'getServicesGroupedByCategory', 'getDistricts']),
            async remoteSearchDistricts(search) {
                if (search === '') {
                    this.districts = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const {data} = await this.getDistricts({get_all: true, search});

                        this.districts = data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            ...mapGetters(['states'])
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
                                const {state_id, city, street, street_nr, zip, ...restParams} = this.model;
                                const response = await this.createBuilding({
                                    address: {
                                        state_id,
                                        city,
                                        street,
                                        street_nr,
                                        zip
                                    },
                                    ...restParams
                                });
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
                    ...mapActions(['createBuilding', 'createAddress'])
                };

                mixin.created = async function () {
                    await this.getStates();
                    const {data} = await this.getServicesGroupedByCategory();
                    this.allServices = data;
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
                                    const {state_id, city, street, street_nr, zip, ...restParams} = this.model;
                                    const data = await this.updateBuilding({
                                        address: {
                                            state_id,
                                            city,
                                            street,
                                            street_nr,
                                            zip
                                        },
                                        ...restParams,
                                        service_providers: restParams.service_providers_ids
                                    });

                                    this.model.service_providers = data.data.service_providers;

                                    this.model.service_providers_ids = [];

                                    displaySuccess(data);
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
                    ...mapActions(['getBuilding', 'updateBuilding', 'updateAddress', 'getBuildingStatistics'])
                };

                mixin.created = async function () {
                    try {
                        this.loading.state = true;

                        await this.getStates();

                        const {data} = await this.getServicesGroupedByCategory();
                        this.allServices = data;

                        const {
                            address: {
                                state: {
                                    id: state_id
                                },
                                ...restAddress
                            },
                            ...restData
                        } = await this.getBuilding({id: this.$route.params.id});


                        this.model = {state_id, ...restAddress, ...restData, service_providers_ids: []};

                        if (this.model.district) {
                            this.$set(this.model, 'district_id', this.model.district.id);
                            this.remoteSearchDistricts(`${this.model.district.name}`);
                        }

                        const {
                            data: {
                                free_units,
                                occupied_units,
                                total_tenants,
                                total_units
                            }
                        } = await this.getBuildingStatistics({id: this.$route.params.id});

                        this.statistics.raw[0].value = total_tenants;
                        this.statistics.raw[1].value = total_units;

                        this.statistics.percentage.occupied_units = occupied_units;
                        this.statistics.percentage.free_units = free_units;
                    } catch (err) {
                        this.$router.replace({
                            name: 'adminBuildings'
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
