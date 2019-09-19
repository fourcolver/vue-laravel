<template>
    <el-form :model="model" ref="form">
        <el-form-item prop="email" :label="$t('email')" :rules="validationRules.email">
            <el-input type="email" v-model="model.email" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item prop="password" :label="$t('password')" :rules="validationRules.password">
            <el-input type="password" v-model="model.password" autocomplete="off"></el-input>
        </el-form-item>
        <el-form-item>
            <el-checkbox>{{$t('remember_me')}}</el-checkbox>
            <router-link :to="{name: 'forgot'}">
                <el-button type="text">
                    {{$t('forgot_password')}}
                </el-button>
            </router-link>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" class="text-center w100p" @click="submit">{{$t('login')}}</el-button>
        </el-form-item>
    </el-form>
</template>
<script>
    import {mapActions, mapState} from 'vuex';
    import {displaySuccess, displayError} from 'helpers/messages';

    export default {
        data() {
            return {
                model: {
                    email: '',
                    password: ''
                },
                validationRules: {
                    email: [{
                        required: true,
                        message: this.$t("email_validation.required")
                    }, {
                        type: 'email',
                        message: this.$t("email_validation.email")
                    }],
                    password: [{
                        required: true,
                        message: this.$t("password_validation.required")
                    }]
                }
            }
        },
        computed: {
            ...mapState({
                loggedInUser: ({users}) => {
                    return users.loggedInUser;
                }
            })
        },
        methods: {
            submit() {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        try {
                            await this.login(this.model);
                            const {data: {settings: {language}}, ...rest} = await this.me();

                            this.$i18n.locale = language;
                            this.$router.push({
                                name: 'tenantDashboard'
                            });

                            displaySuccess(rest);
                        } catch (err) {
                            displayError(err);
                        }
                    }
                });
            },

            ...mapActions(['me', 'login']),
        }
    }
</script>
<style lang="scss" scoped>
    .el-form-item {
        &:nth-last-child(2) :global(.el-form-item__content) {
            display: flex;
            align-items: center;
            :global(.el-checkbox) {
                flex: 1;
                margin: 0;
            }
        }
         .el-button {
            width: 100%;
        }
    }
</style>
