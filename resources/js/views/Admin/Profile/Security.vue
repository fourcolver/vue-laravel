<template>
    <card>
        <el-form :model="model" label-width="224px" ref="form">
            <el-form-item :label="$t('old_password')" :rules="validationRules.password_old"
                          prop="password_old">
                <el-input autocomplete="off" type="password"
                          v-model="model.password_old"></el-input>
            </el-form-item>
            <el-form-item :label="$t('new_password')" :rules="validationRules.password"
                          prop="password">
                <el-input autocomplete="off" type="password"
                          v-model="model.password"></el-input>
            </el-form-item>
            <el-form-item :label="$t('new_password_confirmation')" :rules="validationRules.password_confirmation"
                          prop="password_confirmation">
                <el-input autocomplete="off" type="password"
                          v-model="model.password_confirmation"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button @click="submit" icon="ti-save" type="primary">
                    {{$t('change')}}
                </el-button>
            </el-form-item>
        </el-form>
    </card>
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Card from 'components/Card';
    import PasswordValidatorMixin from 'mixins/passwordValidatorMixin';

    export default {
        name: 'AdminSettingsSecurity',
        mixins: [PasswordValidatorMixin()],
        components: {
            Card
        },
        data() {
            return {
                model: {
                    password_old: '',
                    password: '',
                    password_confirmation: ''
                },
                validationRules: {
                    password_old: [{
                        required: true,
                        message: this.$t("password_validation.old_password_required")
                    }],
                    password: [{
                        validator: this.validatePassword
                    }, {
                        min: 6,
                        message: this.$t("password_validation.min")
                    }],
                    password_confirmation: [{
                        validator: this.validateConfirmPassword
                    }]
                }
            };
        },
        methods: {
            ...mapActions(['changeUserPassword']),
            submit() {
                this.form.validate(async valid => {
                    if (valid) {
                        try {
                            displaySuccess(await this.changeUserPassword(this.model));

                            this.form.resetFields();
                        } catch (err) {
                            displayError(err);
                        }
                    }
                });
            }
        },
        computed: {
            ...mapState({
                user: ({users}) => users.loggedInUser
            }),

            form() {
                return this.$refs.form;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card {
        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51620031_23843322451160669_1044943222171762688_n.png');
            background-repeat: no-repeat;
            background-size: 30em;
            background-position: top right;
        }

        .el-form {
            max-width: 512px;

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
