<template>
    <div class="services-edit mb20">
        <heading :title="$t('models.service.edit_title')" icon="ti-user" shadow="heavy">
            <edit-actions :saveAction="submit" :deleteAction="deleteService" route="adminServices"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-divider class="column-divider" content-position="left">
                            {{$t('models.service.company_details')}}
                        </el-divider>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.service.category')" prop="category">
                                    <el-select :placeholder="$t('models.service.placeholders.category')"
                                               style="width: 100%"
                                               v-model="model.category">
                                        <el-option
                                            :key="category"
                                            :label="$t(`models.service.${category}`)"
                                            :value="category"
                                            v-for="category in serviceCategories">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-divider class="column-divider" content-position="left">
                            {{$t('models.service.user_credentials')}}
                        </el-divider>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.profile_image')">
                                    <cropper :resize="false" :viewportType="'square'" @cropped="cropped"/>
                                    <img :src="`/${model.user.avatar}?${Date.now()}`"
                                         style="width: 100%" v-if="!avatar.length && model.user.avatar">
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.email')" :rules="validationRules.email"
                                              prop="email">
                                    <el-input type="email" v-model="model.email"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('password')" :rules="validationRules.password"
                                              autocomplete="off"
                                              prop="user.password">
                                    <el-input type="password" v-model="model.user.password"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('confirm_password')"
                                              :rules="validationRules.password_confirmation"
                                              prop="user.password_confirmation">
                                    <el-input type="password" v-model="model.user.password_confirmation"/>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-divider class="column-divider" content-position="left">
                            {{$t('models.service.contact_details')}}
                        </el-divider>

                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.address.street')" :rules="validationRules.street"
                                              prop="address.street"
                                              style="max-width: 512px;">
                                    <el-input type="text" v-model="model.address.street"></el-input>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.address.street_nr')" :rules="validationRules.street_nr"
                                              prop="address.street_nr" style="max-width: 512px;">
                                    <el-input type="text" v-model="model.address.street_nr"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.address.zip')" :rules="validationRules.zip"
                                              prop="address.zip"
                                              style="max-width: 512px;">
                                    <el-input type="text" v-model="model.address.zip"></el-input>
                                </el-form-item>
                            </el-col>
                        </el-row>

                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.address.city')" :rules="validationRules.city"
                                              prop="address.city"
                                              style="max-width: 512px;">
                                    <el-input type="text" v-model="model.address.city"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.address.state.label')"
                                              :rules="validationRules.state_id"
                                              prop="address.state_id" style="max-width: 512px;">
                                    <el-select :placeholder="$t('models.address.state.label')" style="display: block"
                                               v-model="model.address.state_id">
                                        <el-option :key="state.id" :label="state.name" :value="state.id"
                                                   v-for="state in states"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </card>
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
                                    :label="$t(`models.service.assignmentTypes.${type}`)"
                                    :value="type"
                                    v-for="(type) in assignmentTypes">
                                </el-option>
                            </el-select>
                        </el-col>
                        <el-col :lg="12" :xl="14">
                            <el-select
                                :loading="remoteLoading"
                                :placeholder="$t('models.service.placeholders.search')"
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
                                {{$t('models.service.assign')}}
                            </el-button>
                        </el-col>
                    </el-row>
                    <relation-list
                        :actions="assignmentsActions"
                        :columns="assignmentsColumns"
                        :filterValue="model.id"
                        fetchAction="getServiceAssignments"
                        filter="provider_id"
                        ref="assignmentsList"
                        v-if="model && model.id"
                    />

                </card>
                <card :loading="loading" class="mt15">
                    <el-divider class="column-divider" content-position="left">{{$t('models.service.requests')}}
                    </el-divider>

                    <relation-list
                        :actions="requestActions"
                        :columns="requestColumns"
                        :filterValue="model.id"
                        fetchAction="getRequests"
                        filter="service_id"
                        v-if="model && model.id"
                    />
                </card>
            </el-col>
        </el-row>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import ServicesMixin from 'mixins/adminServicesMixin';
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";


    export default {
        name: 'AdminServicesEdit',
        mixins: [ServicesMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            EditActions,
            RelationList
        },
        data() {
            return {
                requestColumns: [{
                    prop: 'title',
                    label: this.$t('models.request.prop_title')
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
                    label: this.$t('models.service.assignType'),
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.service.unassign'),
                        type: 'danger',
                        onClick: this.notifyUnassignment
                    }]
                }]
            }
        },
        methods: {
            ...mapActions(['unassignServiceBuilding', 'unassignServiceDistrict', 'deleteService']),
            requestEditView(row) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: row.id
                    }
                })
            },

            notifyUnassignment(row) {
                this.$confirm(this.$t(`models.service.confirmUnassign.title`), this.$t('models.service.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.service.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.service.confirmUnassign.cancelBtnText`),
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
            async unassign(toUnassign) {
                let resp;
                if (toUnassign.aType == 1) {
                    resp = await this.unassignServiceBuilding({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                } else {
                    resp = await this.unassignServiceDistrict({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                }

                if (resp) {
                    this.$refs.assignmentsList.fetch();

                    this.toAssign = '';

                    const type = toUnassign.aType == 1 ? 'building' : 'district';

                    displaySuccess({
                        success: true,
                        message: this.$t(`models.service.detached.${type}`)
                    })
                }
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

    .group-name {
        width: 192px;
        text-align: right;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
    }

    .mb15 {
        margin-bottom: 15px;
    }
</style>
