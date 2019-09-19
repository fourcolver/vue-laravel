import {mapActions} from 'vuex';
import PasswordValidatorMixin from './passwordValidatorMixin';
import TenantTitleTypes from './methods/tenantTitleTypes';
import {displayError, displaySuccess} from '../helpers/messages';
import UploadUserAvatarMixin from './adminUploadUserAvatarMixin';

export default (config = {}) => {
    let mixin = {
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                remoteLoading: false,
                buildings: [],
                units: [],
                user: {},
                model: {
                    first_name: '',
                    last_name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                    birth_date: '',
                    mobile_phone: '',
                    private_phone: '',
                    work_phone: '',
                    title: '',
                    company: '',
                    building_id: '',
                    unit_id: '',
                    media: []
                },
                validationRules: {
                    first_name: [{
                        required: true,
                        message: this.$t('models.tenant.validation.first_name.required')
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('models.tenant.validation.last_name.required')
                    }],
                    email: [{
                        required: true,
                        message: this.$t("email_validation.required")
                    }, {
                        type: 'email',
                        message: this.$t("email_validation.email")
                    }],
                    password: [{
                        validator: this.validatePassword
                    }, {
                        min: 6,
                        message: this.$t("password_validation.min")
                    }],
                    password_confirmation: [{
                        validator: this.validateConfirmPassword,
                    }],
                    birth_date: [{
                        required: true,
                        message: this.$t('models.tenant.validation.birth_date.required')
                    }],
                    building_id: [{
                        required: true,
                        message: this.$t('models.tenant.validation.building.required')
                    }],
                    unit_id: [{
                        required: true,
                        message: this.$t('models.tenant.validation.unit.required')
                    }],
                    title: [{
                        required: true,
                        message: this.$t('models.tenant.validation.title.required')
                    }]
                },
                loading: {
                    state: false,
                    text: 'Please wait...'
                },
                avatar: ''
            };
        },
        methods: {
            isFileImage (file) {
                const ext = file.name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext);
            },
            isFilePDF (file) {
                const ext = file.name.split('.').pop()
                return ['.pdf'].includes(ext);
            },
            async remoteSearchBuildings(search) {
                if (search === '') {
                    this.buildings = [];
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getBuildings({get_all: true, search});
                        this.buildings = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            async searchUnits() {
                this.model.unit_id = '';
                try {
                    const resp = await this.getUnits({
                        get_all: true,
                        building_id: this.model.building_id
                    });

                    this.units = resp.data;
                } catch (err) {
                    displayError(err);
                } finally {
                    this.remoteLoading = false;
                }
            },
            disabledRentStart(date) {
                const d = new Date(date).getTime();
                const rentEnd = new Date(this.model.rent_end).getTime();
                return d >= rentEnd;
            },
            disabledRentEnd(date) {
                const d = new Date(date).getTime();
                const rentStart = new Date(this.model.rent_start).getTime();
                return d <= rentStart;
            },
            contractUploaded(file) {
                this.uploadMediaFile({
                    id: this.model.id,
                    media: file.src
                }).then(r => {
                    displaySuccess(r);

                    this.model.media.push(r.data);
                }).catch(err => {
                    displayError(err);
                });
            },

            ...mapActions(['getBuildings', 'getUnits', 'uploadMediaFile']),
        },
        computed: {
            form() {
                return this.$refs.form;
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.mixins = [PasswordValidatorMixin(), TenantTitleTypes, UploadUserAvatarMixin];

                mixin.methods = {
                    async contractUpl(id) {
                        return await this.uploadMediaFile({
                            id,
                            media: this.toUploadContract.src
                        }).then(r => {
                            displaySuccess(r);
                        }).catch(err => {
                            displayError(err);
                        });
                    },
                    async submit() {
                        const valid = await this.form.validate();
                        if (!valid) {
                            return false;
                        }

                        this.loading.state = true;

                        let {email, password, password_confirmation, ...tenant} = this.model;

                        try {

                            const resp = await this.createTenant({
                                user: {
                                    email,
                                    password,
                                    password_confirmation: password_confirmation
                                },
                                ...tenant
                            });

                            if (resp.data.user && resp.data.user.id) {
                                this.uploadAvatarIfNeeded(resp.data.user.id);
                            }

                            if (resp.data && resp.data.id && !_.isEmpty(this.toUploadContract)) {
                                await this.contractUpl(resp.data.id);
                            }

                            displaySuccess(resp);

                            this.toUploadContract = {};
                            this.model.rent_start = '';
                            this.form.resetFields();
                            return resp;
                        } catch (err) {
                            displayError(err);
                        } finally {
                            this.loading.state = false;
                        }
                    },

                    ...mixin.methods,
                    ...mapActions(['createTenant'])
                };

                break;
            case 'edit':
                mixin.mixins = [PasswordValidatorMixin({required: false}), TenantTitleTypes, UploadUserAvatarMixin];

                mixin.methods = {
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                let {password_confirmation, ...params} = this.model;

                                if (params.password === '') {
                                    params = _.omit(params, ['password'])
                                }

                                try {
                                    const resp = await this.updateTenant(params);

                                    if (resp.data.user && resp.data.user.id) {
                                        this.uploadAvatarIfNeeded(resp.data.user.id);
                                    }

                                    displaySuccess(resp);
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
                    ...mapActions(['getTenant', 'updateTenant'])
                };

                mixin.computed = {
                    ...mixin.computed
                };

                mixin.created = async function () {
                    const {password, password_confirmation} = this.validationRules;

                    [...password, ...password_confirmation].forEach(rule => rule.required = false);

                    try {
                        this.loading.state = true;

                        const {address, building, unit, user, ...r} = await this.getTenant({id: this.$route.params.id});
                        this.user = user;
                        this.model = Object.assign({}, this.model, r);
                        this.model.email = user.email;
                        this.model.avatar = user.avatar;
                        if (building) {
                            this.model.building_id = building.id;
                            this.remoteSearchBuildings(building.name);
                        }
                        if (unit) {
                            await this.searchUnits();
                            this.model.unit_id = unit.id;
                        }

                    } catch (err) {
                        this.$router.replace({
                            name: 'adminTenants'
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
