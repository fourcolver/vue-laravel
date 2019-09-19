<template>
    <div class="services-edit">
        <heading :title="$t('models.propertyManager.edit_title')" icon="ti-user" shadow="heavy">
            <edit-actions :saveAction="submit" :deleteAction="deletePropertyManager" route="adminPropertyManagers"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <el-tabs type="border-card" v-model="activeTab">
                            <el-tab-pane :label="$t('models.propertyManager.details_card')" name="details">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.firstName')"
                                                      :rules="validationRules.first_name"
                                                      prop="first_name">
                                            <el-input type="text" v-model="model.first_name"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.lastName')"
                                                      :rules="validationRules.last_name"
                                                      prop="last_name">
                                            <el-input type="text" v-model="model.last_name"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.profession')"
                                                      :rules="validationRules.profession"
                                                      prop="profession">
                                            <el-input type="text" v-model="model.profession"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">

                                        <el-form-item :label="$t('models.propertyManager.slogan')"
                                                      :rules="validationRules.slogan"
                                                      prop="slogan">
                                            <el-input type="text" v-model="model.slogan"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>


                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.phone')" prop="user.phone">
                                            <el-input type="text" v-model="model.user.phone"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.email')"
                                                      :rules="validationRules.email"
                                                      prop="user.email">
                                            <el-input type="email" v-model="model.user.email"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.propertyManager.profile_card')" name="profile">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.password')"
                                                      :rules="validationRules.password"
                                                      autocomplete="off"
                                                      prop="user.password">
                                            <el-input type="password" v-model="model.user.password"/>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.confirm_password')"
                                                      :rules="validationRules.password_confirmation"
                                                      prop="user.password_confirmation">
                                            <el-input type="password" v-model="model.user.password_confirmation"/>
                                        </el-form-item>
                                    </el-col>
                                </el-row>

                                <el-form-item :label="$t('models.user.profile_image')">
                                    <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                                    <img :src="`/${model.user.avatar}?${Date.now()}`"
                                         style="width: 100%;max-width: 200px;"
                                         v-if="!avatar.length && model.user.avatar">
                                </el-form-item>
                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.propertyManager.social_card')" name="social">
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.linkedin_url')"
                                                      :rules="validationRules.linkedin_url"
                                                      prop="linkedin_url">
                                            <el-input type="text" v-model="model.linkedin_url">
                                                <template slot="prepend"><i class="icon-linkedin"></i></template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.propertyManager.xing_url')"
                                                      :rules="validationRules.xing_url"
                                                      prop="xing_url">
                                            <el-input type="text" v-model="model.xing_url">
                                                <template slot="prepend"><i class="icon-xing"></i></template>
                                            </el-input>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </el-tab-pane>
                        </el-tabs>
                    </el-col>
                    <el-col :md="12">
                        <card :loading="loading">
                            <el-row :gutter="10">
                                <el-col :lg="6">
                                    <el-select @change="resetToAssignList"
                                               class="custom-select"
                                               v-model="assignmentType"
                                    >
                                        <el-option
                                            :key="type"
                                            :label="$t(`models.propertyManager.assignmentTypes.${type}`)"
                                            :value="type"
                                            v-for="(type) in assignmentTypes">
                                        </el-option>
                                    </el-select>
                                </el-col>
                                <el-col :lg="12" :xl="14">
                                    <el-select
                                        :loading="remoteLoading"
                                        :placeholder="$t('models.propertyManager.placeholders.search')"
                                        :remote-method="remoteSearchBuildings"
                                        class="custom-remote-select"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="toAssign"
                                    >
                                        <div class="custom-prefix-wrapper" slot="prefix">
                                            <i class="el-icon-search custom-icon"></i>
                                        </div>
                                        <el-option
                                            :key="building.id"
                                            :label="building.name"
                                            :value="building.id"
                                            v-for="building in toAssignList"/>
                                    </el-select>
                                </el-col>
                                <el-col :lg="6" :xl="4">
                                    <el-button :disabled="!toAssign" @click="attachBuilding" class="full-button"
                                               icon="ti-save" type="primary">
                                        {{$t('models.propertyManager.assign')}}
                                    </el-button>
                                </el-col>
                            </el-row>
                            <relation-list
                                :actions="assignmentsActions"
                                :columns="assignmentsColumns"
                                :filterValue="model.id"
                                fetchAction="getAssignments"
                                filter="manager_id"
                                ref="assignmentsList"
                                v-if="model.id"
                            />
                        </card>
                        <card :loading="loading" class="mt15">
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.propertyManager.requests')}}
                            </el-divider>

                            <relation-list
                                :actions="requestActions"
                                :columns="requestColumns"
                                :filterValue="model.user.id"
                                fetchAction="getRequests"
                                filter="assignee_id"
                                v-if="model.user && model.user.id"
                            />
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import PropertyManagersMixin from 'mixins/adminPropertyManagersMixin';
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import {mapGetters, mapActions} from 'vuex';
    import globalFunction from "helpers/globalFunction";

    export default {
        name: 'AdminPropertyManagersEdit',
        mixins: [globalFunction, PropertyManagersMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            RelationList,
            EditActions
        },
        data() {
            return {
                activeTab: "details",
                requestColumns: [{
                    prop: 'category.name',
                    label: this.$t('models.request.category')
                }, {
                    prop: 'status',
                    withBadge: this.requestStatusBadge,
                    label: this.$t('models.request.status.label'),
                    i18n: this.translateRequestStatus
                }],
                requestActions: [{
                    width: '90px',
                    buttons: [{
                        title: this.$t('models.request.edit'),
                        type: 'primary',
                        onClick: this.requestEditView
                    }]
                }],
                assignmentsColumns: [{
                    prop: 'name',
                    label: this.$t('models.district.name')
                }, {
                    prop: 'type',
                    label: this.$t('models.propertyManager.assignType'),
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.propertyManager.unassign'),
                        type: 'danger',
                        onClick: this.notifyUnassignment
                    }]
                }]
            }
        },
        methods: {
            ...mapActions(['deletePropertyManager']),
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },
            translateType(type) {
                return this.$t(`models.propertyManager.assignmentTypes.${type}`);
            },
            translateRequestStatus(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`);
            },
            notifyUnassignment(row) {
                this.$confirm(this.$t(`models.propertyManager.confirmUnassign.title`), this.$t('models.propertyManager.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.propertyManager.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.propertyManager.confirmUnassign.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassign(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },

            requestStatusBadge(status) {                
                return this.getRequestStatusColor(status);
            },
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            requestStatusConstants() {
                return this.constants.serviceRequests.status
            }
        }
    }
</script>

<style lang="scss" scoped>
    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>
