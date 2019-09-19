<template>
    <el-card>
        <el-form label-width="192px" ref="form">
            <el-form-item :label="$t('settings.summary.label')">
                <el-select v-model="user.settings.summary">
                    <el-option :key="summary" :label="$t('settings.summary.' + summary )" :value="summary"
                               v-for="summary in summaryValues"></el-option>
                </el-select>
            </el-form-item>
            <el-form-item :label="$t('settings.service')">
                <el-switch v-model="user.settings.service_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.news')">
                <el-switch v-model="user.settings.news_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.admin')">
                <el-switch v-model="user.settings.admin_notification"></el-switch>
            </el-form-item>
            <el-form-item :label="$t('settings.language')">
                <el-radio-group v-model="user.settings.language">
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
                <el-button @click="settingsUpdated" icon="ti-save" type="primary">{{$t('models.user.save')}}</el-button>
            </el-form-item>
        </el-form>

    </el-card>
</template>

<script>
    import {mapGetters, mapState, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';

    export default {
        name: 'AdminSettingsAccount',
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
                    name: [
                        {
                            required: true,
                            message: this.$t("models.user.validation.name.required")
                        }
                    ],
                },
                summaryValues: [
                    "daily", "monthly", "yearly"
                ],
            };
        },

        computed: {
            ...mapGetters(["getAllAvailableLanguages"]),
            ...mapState({
                user: ({users}) => users.loggedInUser
            })
        },

        methods: {
            ...mapActions(['updateSettings']),

            settingsUpdated() {

                displaySuccess({
                    message: this.$t('settings.updated'),
                });

                this.updateSettings(this.user).then((resp) => {

                }).catch((err) => {
                    displayError(err);
                })

            },
        },

    }
</script>

<style lang="scss" scoped>
    .el-card {
        border-radius: 6px;
        box-shadow: 0 1px 3px transparentize(#000, .88),
        0 1px 2px transparentize(#000, .76);
        position: relative;

        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51554884_23843403016020020_4498652095628443648_n.png');
            background-repeat: no-repeat;
            background-size: contain;
            background-position: right bottom;
        }

        .el-form {
            /*max-width: 512px;*/

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
