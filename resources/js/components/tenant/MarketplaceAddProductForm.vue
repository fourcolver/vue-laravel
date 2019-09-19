<template>
    <el-form ref="form" class="add-product-form" :model="model" :rules="validationRules" label-position="top" v-loading="loading">
        <el-form-item prop="title" label="Title">
            <el-input v-model="model.title" />
        </el-form-item>
        <el-row type="flex">
            <el-col>
                <el-form-item prop="type" label="Type">
                    <el-select v-model="model.type">
                        <el-option v-for="category in types" :key="category.value" :label="category.label" :value="category.value" />
                    </el-select>
                </el-form-item>
            </el-col>
            <el-col v-if="canShowPrice">
                <el-form-item prop="price" label="Price">
                    <el-row type="flex">
                        <el-col>
                            <el-input v-model="model.price.integer" />
                        </el-col>
                        <el-col style="flex: 1;">.</el-col>
                        <el-col>
                            <el-input v-model="model.price.decimals" />
                        </el-col>
                    </el-row>
                </el-form-item>
            </el-col>
             <el-col>
                <el-form-item prop="visibility" label="Visibility">
                    <el-select v-model="model.visibility">
                        <el-option v-for="visibility in visibilities" :key="visibility.value" :label="visibility.label" :value="visibility.value" />
                    </el-select>
                </el-form-item>
            </el-col>
        </el-row>
        <el-form-item prop="content" label="Content">
            <el-input type="textarea" resize="none" v-model="model.content" :autosize="{minRows: 4, maxRows: 16}" />
        </el-form-item>
        <el-row type="flex">
            <el-col>
                <el-form-item prop="tenant_name" label="Contact name">
                    <el-input v-model="model.tenant_name" />
                </el-form-item>
            </el-col>
            <el-col>
                <el-form-item prop="tenant_phone" label="Contact phone">
                    <el-input v-model="model.tenant_phone" />
                </el-form-item>
            </el-col>
        </el-row>
        <media-upload ref="upload" v-model="model.media" :size="mediaUploadMaxSize" :allowed-types="['image/jpg', 'image/jpeg', 'image/png']" :cols="5" />
        <el-form-item v-if="showSubmit">
            <el-button class="submit" type="primary" @click="submit">Save</el-button>
        </el-form-item>
    </el-form>
</template>

<script>
    import {MEDIA_UPLOAD_MAX_SIZE} from '@/config'
    import MediaUpload from 'components/MediaUpload'
    import {displaySuccess, displayError} from 'helpers/messages'
    import PQueue from 'p-queue'

    export default {
        props: {
            showSubmit: {
                type: Boolean,
                default: false
            }
        },
        components: {
            MediaUpload
        },
        data () {
            return {
                types: [],
                loading: false,
                model: {
                    media: [],
                    type: null,
                    title: null,
                    price: {
                        integer: '0',
                        decimals: '00'
                    },
                    content: null,
                    visibility: null,
                    tenant_name: null,
                    tenant_phone: null
                },
                validationRules: {
                    type: {
                        required: true,
                        message: 'This field is required'
                    },
                    title: {
                        required: true,
                        message: 'This field is required'
                    },
                    price: {
                        validator: this.priceValidator
                    },
                    content: {
                        required: true,
                        message: 'This field is required'
                    },
                    visibility: {
                        required: true,
                        message: 'This field is required'
                    },
                    tenant_name: {
                        required: true,
                        message: 'This field is required'
                    },
                    tenant_phone: {
                        required: true,
                        message: 'This field is required'
                    }
                },
                mediaUploadMaxSize: MEDIA_UPLOAD_MAX_SIZE
            }
        },
        methods: {
            submit () {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        try {
                            this.loading = true;

                            const {price, media, tenant_name, tenant_phone, ...params} = this.model

                            params.price = `${price.integer}.${price.decimals}`
                            params.contact = `${tenant_name} - ${tenant_phone}`

                            const {data} = await this.$store.dispatch('products2/create', params);

                            if (this.model.media.length) {
                                const queue = new PQueue({concurrency: 1})

                                media.forEach(({file}) => queue.add(async () => await this.$store.dispatch('media/upload', {
                                    id: data.data.id,
                                    media: file.src,
                                    type: 'products'
                                })))

                                await queue.onIdle()
                            }

                            displaySuccess(data)

                            this.$refs.form.resetFields()
                            this.$refs.upload.clear()
                        } catch (err) {
                            displayError(err);
                        } finally {
                            this.loading = false;
                        }
                    }
                });
            },
            priceValidator (rule, value, callback) {
                const integer = +(value.integer || undefined)
                const decimals = +(value.decimals || undefined)

                if (!isNaN(integer) &&
                    !isNaN(decimals) &&
                    integer % 1 === 0 &&
                    decimals % 1 === 0 &&
                    decimals >= 0 && decimals <= 99 &&
                    integer >= 0 && integer <= Number.MAX_SAFE_INTEGER
                ) {
                    callback()
                } else {
                    callback(new Error('The price is invalid'))
                }
            }
        },
        computed: {
            canShowPrice () {
                return this.model.type != (Object.entries(this.$constants.products.type).find(([_, name]) => name === 'giveaway') || [])[0]
            }
        },
        created () {
            const {first_name, last_name, mobile_phone} = this.$store.getters.loggedInUser.tenant

            this.model.tenant_name = `${first_name} ${last_name}`
            this.model.tenant_phone = mobile_phone

            this.types = Object.entries(this.$constants.products.type).map(([value, label]) => ({value: +value, label}))
            this.visibilities = Object.entries(this.$constants.products.visibility).map(([value, label]) => ({value: +value, label}))
            
            if (this.types.length) {
                this.model.type = this.types[0].value
            }

            if (this.visibilities) {
                this.model.visibility = this.visibilities[0].value
            }
        }
    };
</script>

<style lang="scss" scoped>
    .add-product-form {
        .el-row {
            .el-col:not(:last-of-type) {
                margin-right: 8px;
            }
        }
        .el-form-item {
            .el-select {
                width: 100%;
            }
        }
        .el-button.submit {
            margin-top: 1em;
        }
    }
</style>