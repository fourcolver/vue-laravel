<template>
    <div class="settings">
        <heading :title="$t('models.realEstate.title')" class="custom-heading" icon="ti-settings" shadow="heavy" />
        <el-tabs tab-position="left" v-model="activeName">
            <el-tab-pane :label="$t('models.realEstate.details')" name="details">
                <el-card>
                    <el-form :model="model" label-width="128px" ref="realEstateForm">
                        <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                            <el-input type="text" v-model="model.name"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.user.email')" :rules="validationRules.email" prop="email">
                            <el-input type="email" v-model="model.email"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.user.phone')" prop="phone">
                            <el-input type="string" v-model="model.phone"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.address.street')" :rules="validationRules.street"
                                      prop="address.street">
                            <el-input autocomplete="off" type="text" v-model="model.address.street"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.address.street_nr')" :rules="validationRules.street_nr"
                                      prop="address.street_nr">
                            <el-input autocomplete="off" type="text" v-model="model.address.street_nr"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.address.zip')" :rules="validationRules.zip" prop="address.zip">
                            <el-input autocomplete="off" type="text" v-model="model.address.zip"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.address.city')" :rules="validationRules.city"
                                      prop="address.city">
                            <el-input autocomplete="off" type="text" v-model="model.address.city"></el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.address.state.label')" :rules="validationRules.state_id"
                                      prop="address.state.id">
                            <el-select :placeholder="$t('models.address.state.label')" style="display: block"
                                       v-model="model.address.state.id">
                                <el-option :key="state.id" :label="state.name" :value="state.id"
                                           v-for="state in states"></el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item>
                            <el-button @click="saveRealEstate('realEstateForm')" icon="ti-save" type="primary">
                                {{$t('actions.save')}}
                            </el-button>
                        </el-form-item>
                    </el-form>

                </el-card>
            </el-tab-pane>
            <el-tab-pane :label="$t('models.realEstate.settings')" name="settings">
                <el-row :gutter="20">
                    <el-col :md="12" class="mb20">
                        <el-card>
                            <el-form :model="model" :rules="validationRules" label-width="200px"
                                     ref="realEstateSettingsForm">
                                <el-form-item :label="$t('models.user.logo')">
                                    <cropper @cropped="setLogoUpload"/>
                                    <img :src="realEstateLogo" ref="realEstateLogo"
                                         v-show="realEstateLogo || model.logo_upload"
                                         width="300px">
                                </el-form-item>
                                <el-form-item :label="$t('models.user.blank_pdf')" prop="blank_pdf">
                                    <el-switch v-model="model.blank_pdf"/>
                                </el-form-item>
                                <!-- <el-form-item :label="$t('models.realEstate.district_enable')" prop="district_enable">
                                    <el-switch v-model="model.district_enable"/>
                                </el-form-item>
                                <el-form-item :label="$t('models.realEstate.marketplace_approval_enable')"
                                              prop="marketplace_approval_enable">
                                    <el-switch v-model="model.marketplace_approval_enable"/>
                                </el-form-item> -->
                                <el-form-item :label="$t('models.realEstate.news_approval_enable')"
                                              prop="news_approval_enable">
                                    <el-switch v-model="model.news_approval_enable"/>
                                </el-form-item>
                                <el-form-item :label="$t('models.realEstate.contact_enable')"
                                              prop="contact_enable">
                                    <el-switch v-model="model.contact_enable"/>
                                </el-form-item>
                                <el-form-item :label="$t('models.realEstate.comment_update_timeout')"
                                              :rules="validationRules.comment_update_timeout"
                                              prop="comment_update_timeout">
                                    <el-input autocomplete="off" type="number"
                                              v-model="model.comment_update_timeout"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('models.realEstate.iframe_url.label')"
                                              :rules="validationRules.iframe_url"
                                              prop="iframe_url">
                                    <el-input autocomplete="off" type="text"
                                              v-model="model.iframe_url"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('models.realEstate.cleanify_email')"
                                              :rules="validationRules.cleanify_email" prop="cleanify_email">
                                    <el-input type="email" v-model="model.cleanify_email"></el-input>
                                </el-form-item>
                                <el-form-item>
                                    <el-button @click="saveRealEstate('realEstateSettingsForm')" icon="ti-save"
                                               type="primary">
                                        {{$t('actions.save')}}
                                    </el-button>
                                </el-form-item>
                            </el-form>
                        </el-card>
                    </el-col>
                    <el-col :md="12">
                        <el-card class="mb20">
                            <el-form :model="model" size="mini"
                            >
                                <div :key="schedule.day" :md="12"
                                     class="day-wrapper mb20" v-for="schedule in model.opening_hours ">
                                    <div class="day-name">
                                        <div class="group-name">{{$t(`days.${schedule.day}`)}}</div>
                                        <el-switch
                                            size="mini"
                                            v-model="schedule.closed"
                                        >
                                        </el-switch>
                                    </div>
                                    <el-time-picker
                                        :end-placeholder="$t('models.realEstate.endTime')"
                                        :range-separator="$t('models.realEstate.to')"
                                        :start-placeholder="$t('models.realEstate.startTime')"
                                        format="HH:mm"
                                        is-range
                                        style="width: 100%"
                                        v-model="schedule.time"
                                        value-format="HH:mm">
                                    </el-time-picker>
                                </div>
                            </el-form>
                        </el-card>
                    </el-col>
                </el-row>
            </el-tab-pane>
            <el-tab-pane :label="$t('models.realEstate.categories')" name="categories">
                <CategoriesListing/>
            </el-tab-pane>
            <el-tab-pane :label="$t('models.realEstate.templates')" name="templates">
                <TemplatesListing/>
            </el-tab-pane>
        </el-tabs>
    </div>
</template>
<script>
    import Heading from 'components/Heading';
    import Cropper from 'components/Cropper';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import CategoriesListing from './Categories'
    import TemplatesListing from '../Templates'

    export default {
        name: 'AdminProfile',
        components: {
            Heading,
            Cropper,
            CategoriesListing,
            TemplatesListing
        },
        data() {
            return {
                model: {
                    name: '',
                    email: '',
                    phone: '',
                    blank_pdf: true,
                    district_enable: true,
                    logo: '',
                    logo_upload: '',
                    marketplace_approval_enable: true,
                    news_approval_enable: false,
                    comment_update_timeout: 60,
                    iframe_url: '',
                    cleanify_email: '',
                    address: {
                        state: {}
                    },
                    contact_enable: false
                },
                validationRules: {
                    email: [{
                        required: true,
                        message: this.$t("email_validation.required")
                    },
                        {
                            type: 'email',
                            message: this.$t("email_validation.email")
                        }
                    ],
                    name: [{
                        required: true,
                        message: this.$t("models.user.validation.name.required")
                    }],
                    iframe_url: [{
                        type: 'url',
                        message: this.$t("models.realEstate.iframe_url.validation")
                    }],
                    cleanify_email: [{
                        type: 'email',
                        message: this.$t("email_validation.email")
                    }]
                },
                activeName: 'details',
                states: []
            }
        },
        async created() {
            await this.fetchRealEstate();

            if (this.$route.query.tab) {
                this.goToTab(this.$route.query.tab);
            }
        },
        computed: {
            realEstateLogo() {
                return this.model.logo ? `/${this.model.logo}?${Date.now()}` : '';
            }
        },
        methods: {
            ...mapActions(['getRealEstate', 'updateRealEstate', 'getStates']),
            goToTab(tabName) {
                this.activeName = tabName;
            },
            fetchRealEstate() {
                this.getStates().then((resp) => {
                    this.states = resp.data;
                });
                this.getRealEstate().then((resp) => {
                    this.model = Object.assign({}, this.model, resp.data);
                    try {
                        this.$set(this.model, 'opening_hours', JSON.parse(this.model.opening_hours));
                    } catch (e) {

                    }

                }).catch((error) => {
                    displayError(error);
                });
            },
            saveRealEstate(form) {
                this.$refs[form].validate((valid) => {
                    if (valid) {
                        console.log(valid);
                        this.updateRealEstate(this.model).then((resp) => {
                            this.fetchRealEstate();
                            displaySuccess(resp);
                        }).catch((error) => {
                            displayError(error);
                        });
                    }
                });
            },
            setLogoUpload(image) {
                this.model.logo_upload = image;
            },
        },
        watch: {
            activeName(newTab, oldTab) {
                this.$router.push({
                    name: 'adminSettings',
                    query: {
                        tab: newTab
                    }
                });
            }
        }
    }

</script>
<style lang="scss" scoped>
    .settings {
        /*height: 100% !important;*/

        .custom-heading {
            margin-bottom: 20px;
        }

        .el-card {
            border-radius: 6px;
            box-shadow: 0 1px 3px transparentize(#000, .88),
            0 1px 2px transparentize(#000, .76);
            position: relative;

            .el-form {
                max-width: 512px;

                .el-button :global([class*="ti"]) {
                    margin-right: 8px;
                }
            }
        }
    }

    .group-name {
        text-align: left;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
        text-transform: capitalize;
        width: 100px;
    }

    .day-wrapper {
        display: flex;
        justify-content: space-between;
    }

    .day-name {
        display: flex;
        align-items: center;
        margin-right: 20px;
    }

</style>
