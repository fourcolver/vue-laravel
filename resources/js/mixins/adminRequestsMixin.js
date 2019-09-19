import uuid from 'uuid/v1';
import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import UploadDocument from 'components/UploadDocument';
import RequestMedia from 'components/RequestMedia';
import PrepareCategories from 'mixins/methods/prepareCategories';

export default (config = {}) => {
    let mixin = {
        mixins: [PrepareCategories],
        components: {
            UploadDocument,
            RequestMedia
        },
        props: {
            title: {
                type: String,
                required: true
            }
        },
        data() {
            return {
                model: {
                    title: '',
                    category: '',
                    priority: '',
                    visibility: '',
                    provider_ids: []
                },
                validationRules: {
                    title: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    category: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    priority: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    qualification: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    status: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    due_date: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    description: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }],
                    visibility: [{
                        required: true,
                        message: this.$t('validation.general.required')
                    }]
                },
                loading: {
                    state: false,
                    text: 'Please wait...'
                },
                remoteLoading: false,
                categories: [],
                tenants: [],
                toAssignList: [],
                media: [],
                assignmentTypes: ['managers', 'services'],
                assignmentType: 'managers',
                toAssign: '',
                conversations: [],
                address: {},
            };
        },
        computed: {
            form() {
                return this.$refs.form;
            },
        },
        methods: {
            ...mapActions(['getRequestCategoriesTree', 'getTenants', 'getServices', 'uploadRequestMedia', 'deleteRequestMedia', 'getUsers', 'assignProvider', 'assignManager']),
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
            },
            async remoteSearchAssignees(search) {

                if (!this.$can(this.$permissions.assign.request)) {
                    return false;
                }

                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        let resp = [];
                        if (this.assignmentType === 'managers') {
                            resp = await this.getUsers({
                                get_all: true,
                                search,
                                roles: ['administrator', 'manager']
                            });
                        } else {
                            resp = await this.getServices({get_all: true, search});
                        }

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = '';
            },
            async assignUser() {
                if (!this.toAssign || !this.model.id) {
                    return false;
                }
                let resp;

                if (this.assignmentType === 'managers') {
                    resp = await this.assignManager({
                        request: this.model.id,
                        toAssignId: this.toAssign
                    });
                } else {
                    resp = await this.assignProvider({
                        request: this.model.id,
                        toAssignId: this.toAssign
                    });
                }

                if (resp && resp.data) {
                    await this.fetchCurrentRequest();
                    this.toAssign = '';
                    this.$refs.assigneesList.fetch();
                    displaySuccess({
                        success: true,
                        message: this.$t(`models.request.attached.${this.assignmentType}`)
                    })
                }
            },
            uploadFiles(file) {
                const allowedFiles = [
                    'jpeg', 'png'
                ];
                const extension = file.raw.type.split('/');
                if (extension[1] && allowedFiles.includes(extension[1])) {
                    const url = `data:${file.raw.type};base64,${file.src}`;
                    this.media.push({
                        url,
                        id: uuid()
                    });
                } else {
                    displayError({
                        success: false,
                        message: this.$t('errors.files_extension_images')
                    });
                }
            },
            async uploadNewMedia(id) {
                if (this.media.length) {
                    for (let i = 0; i < this.media.length; i++) {
                        const image = this.media[i];
                        await this.uploadRequestMedia({
                            id,
                            media: image.url.split('base64,')[1]
                        });
                    }
                }
            },
            async deleteMedia(image) {
                if (!image.model_id) {
                    this.media = this.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('models.request.media.removed')
                    });
                } else {
                    await this.deleteRequestMedia({
                        id: image.model_id,
                        media_id: image.id
                    });
                    this.model.media = this.model.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('models.request.media.deleted')
                    });
                }
            },
            selectedCategoryHasQualification(categoryId) {
                if (!categoryId) {
                    return false;
                }

                const categoryArr = this.categories.filter((category) => {
                    return category.id === categoryId && category.has_qualifications;
                });

                if (categoryArr.length) {
                    return true;
                }

                return false;
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createRequest']),
                    async saveRequest() {
                        const resp = await this.createRequest(this.model);

                        await this.uploadNewMedia(resp.data.id);

                        this.media = [];

                        this.form.resetFields();

                        return resp;

                    },
                    async submit() {
                        const valid = await this.form.validate();
                        if (valid) {
                            this.loading.state = true;
                            try {
                                const resp = await this.saveRequest();

                                displaySuccess(resp);
                                return resp;
                            } catch (err) {
                                displayError(err);
                            } finally {
                                this.loading.state = false;
                            }
                        }

                    },
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    this.validationRules.tenant_id = [{
                        required: true,
                        message: 'This field is required'
                    }];

                    const {data: categories} = await this.getRequestCategoriesTree({get_all: true});

                    this.categories = this.prepareCategories(categories);

                    this.loading.state = false;
                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getRequest', 'updateRequest', 'getTenant', 'getRequestConversations', 'getAddress']),
                    async fetchCurrentRequest() {
                        const resp = await this.getRequest({id: this.$route.params.id});

                        const data = resp.data;

                        this.model = Object.assign({}, this.model, data);
                        this.$set(this.model, 'category_id', data.category.id);

                        await this.getConversations();
                        
                        if (data.tenant) {
                            this.model.tenant_id = data.tenant.id;
                            await this.getBuildingAddress(data.tenant.building.id);
                        }
                        
                    },
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }

                                this.loading.state = true;
                                let {providers, assignees, ...params} = this.model;
                                try {
                                    await this.uploadNewMedia(params.id);
                                    const resp = await this.updateRequest(params);
                                    this.media = [];
                                    this.$set(this.model, 'providers', resp.data.providers);
                                    this.$set(this.model, 'media', resp.data.media);
                                    this.$set(this.model, 'assignees', resp.data.assignees);
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
                    async getConversations() {
                        const resp = await this.getRequestConversations({
                            id: this.model.id
                        });

                        if (resp.data) {
                            this.conversations = resp.data;
                        }
                    },
                    async getBuildingAddress(building_id) {
                        const resp = await this.getAddress({
                            id: building_id
                        });
                        if (resp) {
                            this.address = resp;
                        }
                    }
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    const {data: categories} = await this.getRequestCategoriesTree({get_all: true});

                    this.categories = this.prepareCategories(categories);

                    await this.fetchCurrentRequest();

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


