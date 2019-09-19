<template>
    <div>
        <heading
            :title="$t('models.cleanify.pageTitle')"
            class="custom-heading"
            icon="icon-water"
        />
        <el-row :gutter="20" class="mt15">
            <el-col>
                <card :loading="loading" style="max-width: 1024px">
                    <el-form :model="model" :rules="validationRules" label-position="top" label-width="120px"
                             ref="form"
                    >
                        <el-row :gutter="10">
                            <el-col :md="8">
                                <el-form-item :label="$t('models.cleanify.title')" prop="title">
                                    <el-select placeholder="Select" style="display: block" v-model="model.title">
                                        <el-option
                                            :key="title"
                                            :label="$t(`models.tenant.titles.${title}`)"
                                            :value="title"
                                            v-for="title in titles">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('models.cleanify.lastName')" prop="lastName">
                                    <el-input type="text" v-model="model.lastName"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="8">
                                <el-form-item :label="$t('models.cleanify.firstName')" prop="firstName">
                                    <el-input type="text" v-model="model.firstName"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="10">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.cleanify.email')" prop="email">
                                    <el-input type="email" v-model="model.email"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.cleanify.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="10">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.cleanify.zip')" prop="zip"
                                >
                                    <el-input type="text" v-model="model.zip"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.cleanify.city')"
                                              prop="city"
                                >
                                    <el-input type="text" v-model="model.city"></el-input>
                                </el-form-item>

                            </el-col>
                        </el-row>
                        <el-form-item :label="$t('models.cleanify.address')"
                                      prop="address"
                        >
                            <el-input type="text" v-model="model.address"></el-input>
                        </el-form-item>

                        <el-form-item
                            prop="terms"
                        >
                            <el-checkbox v-model="model.terms">{{$t('models.cleanify.terms_and_conditions')}}
                            </el-checkbox>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="submit()" icon="ti-save" type="primary">
                                {{$t('models.cleanify.save')}}
                            </el-button>
                        </el-form-item>

                    </el-form>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";

    export default {
        components: {
            Heading,
            Card
        },
        data() {
            return {
                model: {
                    firstName: '',
                    lastName: '',
                    address: '',
                    zip: '',
                    city: '',
                    email: '',
                    phone: '',
                    title: '',
                    terms: false
                },
                validationRules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("email_validation.required")
                        },
                        {
                            type: 'email',
                            message: this.$t("email_validation.email")
                        }
                    ],
                    lastName: [{
                        required: true,
                        message: this.$t("validation.lastName.required")
                    }],
                    firstName: [{
                        required: true,
                        message: this.$t("validation.firstName.required")
                    }],
                    phone: [{
                        required: true,
                        message: this.$t("validation.phone.required")
                    }],
                    address: [{
                        required: true,
                        message: this.$t("validation.address.required")
                    }],
                    zip: [{
                        required: true,
                        message: this.$t("validation.zip.required")
                    }],
                    city: [{
                        required: true,
                        message: this.$t("validation.city.required")
                    }],
                    title: [{
                        required: true,
                        message: this.$t("validation.title.required")
                    }],
                    terms: [{
                        trigger: 'blue',
                        validator: this.termValidator
                    }]
                },
                titles: ['mr', 'mrs'],
                loading: {
                    state: false,
                    text: 'Please wait...'
                }
            }
        },
        methods: {
            ...mapActions(['sendCleanifyRequest']),
            termValidator(rule, value, callback) {
                if (!value) {
                    callback(new Error(this.$t('validation.terms.required')));
                } else {
                    callback();
                }
            },
            submit() {
                this.$refs.form.validate(async (valid) => {
                    if (!valid) {
                        return false;
                    }

                    this.loading.state = true;

                    try {
                        const payload = Object.assign({}, this.model, {
                            first_name: this.model.firstName,
                            last_name: this.model.lastName
                        });

                        const resp = await this.sendCleanifyRequest(payload);
                        if (resp && resp.data) {
                            displaySuccess({
                                success: true,
                                message: this.$t('models.cleanify.success')
                            });
                            this.$refs.form.resetFields();
                        }
                    } catch (e) {
                        displayError(e);
                    } finally {
                        this.loading.state = false;
                    }
                })
            }
        }
    }
</script>
