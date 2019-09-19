<template>
    <el-form :model="resetPassword" @submit.native.prevent="" ref="resetPasswordEmailForm">
        <el-form-item :label="$t('email')" :rules="validationRules.email" prop="email">
            <el-input autocomplete="off" type="email" v-model="resetPassword.email"></el-input>
        </el-form-item>
        <el-form-item v-loading.lock="loading">
            <el-button @click.prevent="submitResetPasswordForm" class="text-center w100p" type="primary">
                {{$t('reset_password_mail')}}
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

    export default {
        name: 'ForgotPassword',
        data() {
            return {
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
                },
                resetPassword: {
                    email: ''
                },
                loading: false
            }
        },
        methods: {
            ...mapActions(["sendForgotPassword"]),
            submitResetPasswordForm() {
                this.$refs.resetPasswordEmailForm.validate((valid) => {
                    if (!valid) {
                        return false;
                    }

                    this.loading = true;
                    this.sendForgotPassword(this.resetPassword).then((resp) => {
                        this.loading = false;
                        this.$refs.resetPasswordEmailForm.resetFields();
                        displaySuccess(resp);
                    }).catch((err) => {
                        this.loading = false;
                        displayError(err);
                    });
                });
            }
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
