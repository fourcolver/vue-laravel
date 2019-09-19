<template>
    <el-form :model="resetPassword" ref="resetPasswordForm" :rules="validationRules">
        <el-form-item prop="password" :label="$t('password')">
            <el-input type="password" v-model="resetPassword.password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item prop="password_confirmation" :label="$t('confirm_password')">
            <el-input type="password" v-model="resetPassword.password_confirmation" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item v-loading="loading">
            <el-button type="primary"  class="text-center w100p" @click="submitResetPasswordForm">
                {{$t('reset_password')}}
            </el-button>
        </el-form-item>
        <el-form-item>
            <router-link :to="{name: 'login'}" class="w100p">
                <el-button class="text-center w100p">{{$t('back_to_login')}}</el-button>
            </router-link>
        </el-form-item>
    </el-form>
</template>
<script>
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import AsyncValidator from 'async-validator';

    export default {
        name: 'ForgotPassword',
        data() {
            return {
                validationRules: {
                    password: [
                        {
                            validator: this.validatePassword,
                        },
                        {
                            required: true
                        },
                        {
                            min: 6,
                            message: this.$t("password_validation.min")
                        }
                    ],
                    password_confirmation: [
                        {
                            validator: this.validateConfirmPassword,
                        },
                        {
                            required: true
                        }
                    ],
                },
                resetPassword: {
                    email: '',
                    token: '',
                    password: '',
                    password_confirmation: ''
                },
                loading: false
            }
        },
        created() {
            const query = this.$route.query;

            if (_.isEmpty(query)) {
                this.$router.push({name: "login"});
            }

            Object.assign(this.resetPassword, query);
        },
        methods: {
            ...mapActions(["resetPasswordRequest"]),
            submitResetPasswordForm() {
                this.$refs.resetPasswordForm.validate((valid) => {
                    if (valid) {
                        const validator = new AsyncValidator({
                            email: {
                                type: 'email',
                                required: true
                            },
                            token: {
                                required: true
                            }
                        });

                        validator.validate({
                            email: this.resetPassword.email,
                            token: this.resetPassword.token
                        }, (err) => {
                            if (!err) {
                                this.loading = true;
                                this.resetPasswordRequest(this.resetPassword).then((resp) => {
                                    this.loading = false;
                                    this.$router.push({name: "login"});
                                    displaySuccess(resp);
                                }).catch((err) => {
                                    this.loading = false;
                                    displayError(err);
                                });
                            } else {
                                _.each(err, (efield) => {
                                    let message = '';

                                    if (efield.field == "email") {
                                        message = "email_validation.email";
                                    } else if (efield.field == "token") {
                                        message = "token_invalid";
                                    }

                                    displayError({
                                        success: false,
                                        message
                                    });
                                });
                            }
                        });

                    } else {
                        return false;
                    }
                });
            },
            validatePassword(rule, value, callback) {
                if (value === '') {
                    callback(new Error(this.$t("password_validation.required")));
                } else {
                    if (this.resetPassword.password_confirmation !== '') {
                        this.$refs.resetPasswordForm.validateField('password_confirmation');
                    }
                    callback();
                }
            },
            validateConfirmPassword(rule, value, callback) {
                if (value === '') {
                    callback(new Error(this.$t('password_validation.confirm')));
                } else if (value !== this.resetPassword.password) {
                    callback(new Error(this.$t('password_validation.match')));
                } else {
                    callback();
                }
            },
        }
    }
</script>
<style lang="scss" scoped>
    .el-form {
        .el-form-item {
            &:last-of-type :global(.el-form-item__content) {
                display: flex;
                align-items: center;
                .el-checkbox {
                    flex: 1;
                    margin: 0;
                }
            }
            a, .el-button {
                width: 100%;
            }
        }
    }
</style>
