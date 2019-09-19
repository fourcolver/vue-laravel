import {mapActions, mapGetters} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import Heading from 'components/Heading';
import Card from 'components/Card';

export default (config = {}) => {
    let mixin = {
        components: {
            Heading,
            Card
        },
        data() {
            return {
                loading: {
                    state: false,
                    text: 'Please wait...'
                },
                model: {
                    id: '',
                    status: '',
                    body: '',
                    subject: '',
                    name: '',
                    translations: {
                        en: {
                            subject: '',
                            body: ''
                        },
                        fr: {
                            subject: '',
                            body: ''
                        },
                        de: {
                            subject: '',
                            body: ''
                        },
                        it: {
                            subject: '',
                            body: ''
                        }
                    }
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: 'This field is required'
                    }],
                    body: [{
                        required: true,
                        message: 'This field is required'
                    }],
                    subject: [{
                        required: true,
                        message: 'This field is required'
                    }],
                    category: [{
                        required: true,
                        message: 'This field is required'
                    }]
                },
                categories: [],
                selectedCategory: {},
                language: 'de',
                lastElement: ''
            };
        },
        computed: {
            ...mapGetters(['loggedInUser']),
            form() {
                return this.$refs.form;
            },
            categoryTags() {
                return !_.isEmpty(this.selectedCategory) ? this.selectedCategory.tags : [];
            }
        },
        methods: {
            ...mapActions(['getTemplateCategories']),
            insertTag(tag) {
                if (this.lastElement && this.lastElement.target) {
                    this.model.translations[this.language].subject += ` {{${tag}}}`;
                    this.lastElement = '';
                } else {
                    const editor = this.$refs.quillEditor.quill;
                    editor.focus();
                    const selection = editor.getSelection();
                    editor.insertText(selection.index, `{{${tag}}}`);
                }
            },
            setSelectedCategory() {
                this.selectedCategory = {};
                this.categories.forEach((category) => {
                    if (category.categories.length) {
                        category.categories.forEach((subCategory) => {
                            if (subCategory.id === this.model.category_id) {
                                this.selectedCategory = subCategory;
                            }
                        });
                    }
                });
            },
            setLastFocusedElement(event) {
                this.lastElement = event;
                this.$forceUpdate();
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createTemplate']),
                    async submit() {
                        const valid = await this.form.validate();
                        if (!valid) {
                            return false;
                        }

                        this.loading.state = true;

                        try {
                            this.model = Object.assign({}, this.model, this.model.translations[this.loggedInUser.settings.language]);
                            const resp = await this.createTemplate(this.model);
                            displaySuccess(resp);
                            this.form.resetFields();
                            return resp;
                        } catch (err) {
                            displayError(err);
                        } finally {
                            this.loading.state = false;
                        }

                    },
                };

                mixin.created = function () {
                    this.getTemplateCategories().then((resp) => {
                        this.categories = resp;
                        this.language = this.loggedInUser.settings.language;
                    });
                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getTemplate', 'updateTemplate']),
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                try {
                                    this.model = Object.assign({}, this.model, this.model.translations[this.loggedInUser.settings.language]);

                                    const resp = await this.updateTemplate(this.model);
                                    // this.model = Object.assign({}, this.model, resp.data);
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
                    }
                };

                mixin.created = async function () {
                    this.loading.state = true;

                    const resp = await this.getTemplate({id: this.$route.params.id});
                    this.getTemplateCategories().then((resp) => {
                        this.categories = resp;
                        this.setSelectedCategory();
                        this.language = this.loggedInUser.settings.language;
                    });

                    if (_.isEmpty(resp.translations)) {
                        resp.translations = this.model.translations;
                    } else {
                        resp.translations = Object.assign({}, this.model.translations, resp.translations);
                    }

                    this.model = Object.assign({}, this.model, resp);

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


