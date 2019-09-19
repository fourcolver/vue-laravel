import uuid from 'uuid/v1';
import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import Heading from 'components/Heading';
import Card from 'components/Card';
import UploadDocument from 'components/UploadDocument';
import Media from 'components/RequestMedia';


export default (config = {}) => {
    let mixin = {
        components: {
            Heading,
            Card,
            UploadDocument,
            Media
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
                    type: '',
                    visibility: '',
                    content: '',
                    title: '',
                    price: 0,
                    contact: ''
                },
                validationRules: {
                    title: [{
                        required: true,
                        message: 'This field is required'
                    }],
                    content: [{
                        required: true,
                        message: 'This field is required'
                    }],
                    price: [{
                        validator: this.validatePrice
                    }]
                },
                media: [],
                price: {
                    integer: 0,
                    decimals: '00'
                }
            };
        },
        computed: {
            form() {
                return this.$refs.form;
            },
            mediaFiles() {
                return [...this.model.media, ...this.media];
            },
            productConstants() {
                return this.$store.getters['application/constants'].products;
            },
            productPrice() {
                return `${this.price.integer}.${this.price.decimals}`;
            }
        },
        methods: {
            ...mapActions(['uploadProductMedia', 'deleteProductMedia']),
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
                        await this.uploadProductMedia({
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
                        message: this.$t('models.product.media.removed')
                    });
                } else {
                    await this.deleteProductMedia({
                        id: image.model_id,
                        media_id: image.id
                    });
                    this.model.media = this.model.media.filter((files) => {
                        return files.id !== image.id;
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('models.product.media.deleted')
                    });
                }
            },
            validatePrice(rule, value, callback) {
                if (this.model.type !== 4) {
                    const priceInteger = +(this.price.integer || undefined);
                    const priceDecimal = +(this.price.decimals || undefined);


                    if (!isNaN(priceDecimal)
                        && !isNaN(priceInteger)
                        && 0 <= priceInteger
                        && 99 >= priceDecimal
                        && priceDecimal >= 0
                        && priceInteger % 1 === 0
                        && priceDecimal % 1 === 0
                        && this.price.integer.length
                        && this.price.decimals.length
                    ) {
                        callback();
                    } else {
                        callback(new Error(this.$t('validation.price.valid')));
                    }
                } else {
                    callback();
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['createProduct']),
                    submit() {
                        this.form.validate(async valid => {
                            if (valid) {
                                this.loading.state = true;
                                try {
                                    this.model.price = this.productPrice;
                                    const resp = await this.createProduct(this.model);

                                    await this.uploadNewMedia(resp.data.id);

                                    displaySuccess(resp);

                                    this.media = [];

                                    this.form.resetFields();
                                } catch (err) {
                                    displayError(err);
                                } finally {
                                    this.loading.state = false;
                                }
                            }
                        });
                    },
                };

                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods,
                    ...mapActions(['getProduct', 'updateProduct']),
                    submit() {
                        return new Promise((resolve, reject) => {
                            this.form.validate(async valid => {
                                if (!valid) {
                                    resolve(false);
                                    return false;
                                }
                                this.loading.state = true;
                                try {
                                    this.model.price = this.productPrice;
                                    await this.uploadNewMedia(this.model.id);
                                    const resp = await this.updateProduct(this.model);
                                    this.model = Object.assign({}, this.model, resp.data);
                                    this.media = [];
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

                    const resp = await this.getProduct({id: this.$route.params.id});

                    this.model = resp;

                    if (this.model.price) {
                        const modelPrice = this.model.price.split('.');

                        if (modelPrice.length) {
                            this.price = {
                                integer: modelPrice[0],
                                decimals: modelPrice[1]
                            }
                        }
                    }

                    this.loading.state = false;
                };

                break;
        }
    }


    return mixin;
};


