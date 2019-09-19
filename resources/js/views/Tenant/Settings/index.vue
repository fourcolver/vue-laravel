<template>
    <div class="settings">
        <heading class="custom-heading" icon="icon-cog" title="User Settings"/>
        <el-tabs v-model="active">
            <el-tab-pane label="Personal Informations" name="personal_informations">
                <el-row>
                    <el-col :span="12">
                        <card>
                            <el-form :model="loggedInUser" label-width="120px" ref="accform">
                                <el-form-item :label="$t('models.user.profile_image')">
                                    <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                                </el-form-item>
                                <el-form-item :label="$t('models.user.email')" :rules="accountValidationRules.email"
                                              prop="email">
                                    <el-input autocomplete="off" type="email" v-model="loggedInUser.email"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="submitEditDetailsForm()" icon="ti-save" type="primary">
                                        {{$t('models.user.save')}}
                                    </el-button>
                                </el-form-item>
                            </el-form>
                        </card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane label="Security" name="security">
                <el-row>
                    <el-col :span="12">
                        <card>
                            <el-form :model="changePassword" label-width="210px" ref="changePasswordForm" size="medium">
                                <el-form-item :label="$t('old_password')" :rules="passwordValidationRules.password_old"
                                              prop="password_old">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password_old"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('new_password')" :rules="passwordValidationRules.password"
                                              prop="password">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('new_password_confirmation')"
                                              :rules="passwordValidationRules.password_confirmation"
                                              prop="password_confirmation">
                                    <el-input autocomplete="off" type="password"
                                              v-model="changePassword.password_confirmation"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="submitChangePasswordForm" icon="ti-save" type="primary">
                                        {{$t('change')}}
                                    </el-button>
                                    <el-button @click="resetForm">{{$t('cancel')}}</el-button>
                                </el-form-item>
                            </el-form>
                        </card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane :label="$t('settings.notifications')" name="language">
                <card>
                    <el-form label-position="right" label-width="200px">
                        <el-form-item :label="$t('settings.summary.label')">
                            <el-select v-model="loggedInUser.settings.summary">
                                <el-option :key="summary" :label="$t('settings.summary.' + summary )" :value="summary"
                                           v-for="summary in summaryValues"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('settings.service')">
                            <el-switch v-model="loggedInUser.settings.service_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.news')">
                            <el-switch v-model="loggedInUser.settings.news_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.marketplace')">
                            <el-switch v-model="loggedInUser.settings.martketplace_notification"></el-switch>
                        </el-form-item>
                        <el-form-item :label="$t('settings.admin')">
                            <el-switch v-model="loggedInUser.settings.admin_notification"></el-switch>
                        </el-form-item>
                        <el-form-item label="Choose language">
                                <el-radio-group v-model="loggedInUser.settings.language">
                                    <el-radio-button label="fr">
                                        <span class="flag-icon flag-icon-fr"></span> {{$t('languages.fr')}}
                                    </el-radio-button>
                                    <el-radio-button label="de">
                                        <span class="flag-icon flag-icon-de"></span> {{$t('languages.de')}}
                                    </el-radio-button>
                                    <el-radio-button label="en">
                                        <span class="flag-icon flag-icon-us"></span> {{$t('languages.en')}}
                                    </el-radio-button>
                                    <el-radio-button label="it">
                                        <span class="flag-icon flag-icon-it"></span> {{$t('languages.it')}}
                                    </el-radio-button>
                                </el-radio-group>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="settingsUpdated" icon="ti-save" type="primary">Save</el-button>
                        </el-form-item>
                    </el-form>
                </card>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script>
    // TODO: REFACTOR THIS COMPONENT, it is old and has junk code

    import Heading from 'components/Heading';
    import {mapGetters, mapState, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Card from 'components/Card';
    import Cropper from 'components/Cropper';


    export default {
        name: 'TenantSettings',
        components: {
            Card,
            Heading,
            Cropper
        },
        data() {
            return {
                active: 'personal_informations',
                dialogImageUrl: '',
                dialogVisible: false,
                language: 'en',
                content: '',
                imageUrl: '',
                accountValidationRules: {
                    email: [
                        {
                            required: true,
                            message: this.$t("email_validation.required")
                        },
                        {
                            type: 'email',
                            message: this.$t("email_validation.email")
                        }
                    ]
                },
                passwordValidationRules: {
                    password_old: [
                        {
                            required: true,
                            message: this.$t("password_validation.old_password_required")
                        },
                        {
                            min: 6,
                            message: this.$t("password_validation.old_password_min")
                        }
                    ],
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
                changePassword: {
                    password_old: '',
                    password: '',
                    password_confirmation: ''
                },
                image: '',
                summaryValues: [
                    "daily", "monthly", "yearly"
                ]
            };
        },
        computed: {
            ...mapState({
                loggedInUser: ({users}) => {
                    return users.loggedInUser;
                }
            }),
            ...mapGetters(["getAllAvailableLanguages", "loggedInUser"])
        },
        methods: {
            ...mapActions(['updateSettings', 'changeUserPassword', 'changeDetails', 'uploadAvatar', 'me']),
            cropped(e) {
                this.image = e;
            },
            upload() {
                if (this.image) {
                    this.uploadAvatar({
                        image_upload: this.image
                    }).catch((err) => {
                        displayError(err)
                    });
                }
            },
            submitEditDetailsForm() {
                this.$refs.accform.validate(async (valid) => {
                    if (!valid) {
                        return false;
                    }

                    const payload = {
                        name: this.loggedInUser.name,
                        email: this.loggedInUser.email,
                        phone: this.loggedInUser.phone,
                    };

                    try {
                        const resp = await this.changeDetails(payload);
                        await this.upload();
                        await this.me();
                        displaySuccess(resp);
                    } catch (e) {
                        displayError(e);
                    }
                });
            },
            submitChangePasswordForm() {
                this.$refs.changePasswordForm.validate((valid) => {
                    if (valid) {
                        this.changeUserPassword(this.changePassword).then((response) => {
                            displaySuccess(response);
                            this.resetForm();
                        }).catch((err) => {
                            displayError(err);
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
                    if (this.changePassword.password_confirmation !== '') {
                        this.$refs.changePasswordForm.validateField('password_confirmation');
                    }
                    callback();
                }
            },
            validateConfirmPassword(rule, value, callback) {
                if (value === '') {
                    callback(new Error(this.$t('password_validation.confirm')));
                } else if (value !== this.changePassword.password) {
                    callback(new Error(this.$t('password_validation.match')));
                } else {
                    callback();
                }
            },
            resetForm() {
                this.$refs.changePasswordForm.resetFields();
            },
            settingsUpdated() {
                this.updateSettings(this.loggedInUser).then((resp) => {
                    this.$i18n.locale = this.loggedInUser.settings.language;
                    displaySuccess({
                        success: true,
                        message: this.$t('settings.updated')
                    });
                }).catch((err) => {
                    displayError(err);
                });
            },
            handleRemove(file, fileList) {
                console.log(file, fileList);
            },
            handlePictureCardPreview(file) {
                this.dialogImageUrl = file.url;
                this.dialogVisible = true;
            }
        }
    }
</script>

<style lang="scss" scoped>
    .settings {
        .custom-heading {
            margin-bottom: 2em;
        }

        &:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            background-image: url('~img/50761378_23843142320790435_749282303489867776_n.png');
            background-repeat: no-repeat;
            background-position: calc(100% + 6em) calc(100% + 6em);
            width: 100%;
            height: 100%;
        }

        :global(.el-input__prefix) {
            left: 12px;
        }

        .el-button :global(span) {
            margin-left: 8px;
        }

        .el-tabs {
            :global(.el-tabs__content) {
                padding: 2px;

                :global(.el-tab-pane) {
                    .el-row {
                        .el-col:first-of-type {
                            max-width: 768px;;
                        }
                    }

                    &:nth-of-type(3) .el-row .el-col {
                        .el-form {
                            .el-form-item {
                                .el-radio-group {
                                    .el-radio-button {
                                        :global(.el-radio-button__inner) {
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            flex-direction: column;

                                            :global(span) {
                                                margin-bottom: .5em;
                                            }
                                        }
                                    }
                                }

                                :global(.el-form-item__label) {
                                    line-height: 61px;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
