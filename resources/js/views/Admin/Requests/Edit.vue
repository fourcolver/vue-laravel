<template>
    <div class="services-edit mb20" v-if="constants">
        <heading :title="$t('models.request.edit_title')" icon="ti-user" shadow="heavy">
            <edit-actions :saveAction="submit" :deleteAction="deleteRequest" route="adminRequests"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :loading="loading">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.category')"
                                                  :rules="validationRules.category"
                                                  prop="category_id">
                                        <el-select :disabled="$can($permissions.update.serviceRequest)"
                                                   :placeholder="$t('models.request.placeholders.category')"
                                                   class="custom-select"
                                                   v-model="model.category_id">
                                            <el-option
                                                :key="category.id"
                                                :label="category.name"
                                                :value="category.id"
                                                v-for="category in categories">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12"
                                        v-if="model.category_id && selectedCategoryHasQualification(model.category_id)">
                                    <el-form-item :label="$t('models.request.qualification.label')"
                                                  :rules="validationRules.qualification"
                                                  prop="qualification"
                                    >
                                        <el-select :disabled="$can($permissions.update.serviceRequest)"
                                                   :placeholder="$t('models.request.placeholders.qualification')"
                                                   class="custom-select"
                                                   v-model="model.qualification">
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.request.qualification.${qualification}`)"
                                                :value="parseInt(k)"
                                                v-for="(qualification, k) in constants.service_requests.qualification">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.priority.label')"
                                                  :rules="validationRules.priority"
                                                  prop="priority">
                                        <el-select :placeholder="$t('models.request.placeholders.priority')"
                                                   class="custom-select"
                                                   v-model="model.priority"
                                        >
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.request.priority.${priority}`)"
                                                :value="parseInt(k)"
                                                v-for="(priority, k) in constants.service_requests.priority">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.visibility.label')"
                                                  :rules="validationRules.visibility"
                                                  prop="visibility"
                                    >
                                        <el-select :disabled="$can($permissions.update.serviceRequest)"
                                                   :placeholder="$t('models.request.placeholders.visibility')"
                                                   class="custom-select"
                                                   v-model="model.visibility">
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.request.visibility.${visibility}`)"
                                                :value="parseInt(k)"
                                                v-for="(visibility, k) in visibilities">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item v-if="model.tenant">
                                        <label slot="label">
                                            {{$t('models.request.tenant')}}
                                        </label>
                                        <router-link :to="{name: 'adminTenantsEdit', params: {id: model.tenant.id}}"
                                                     class="tenant-link">
                                            <avatar :size="30"
                                                    :src="'/' + model.tenant.user.avatar"
                                                    v-if="model.tenant.user.avatar"></avatar>
                                            <avatar :size="28"
                                                    :username="model.tenant.user.first_name ? `${model.tenant.user.first_name} ${model.tenant.user.last_name}`: `${model.tenant.user.name}`"
                                                    backgroundColor="rgb(205, 220, 57)"
                                                    color="#fff"
                                                    v-if="!model.tenant.user.avatar"></avatar>
                                            <span>{{model.tenant.first_name}} {{model.tenant.last_name}}</span>
                                        </router-link>
                                    </el-form-item>
                                </el-col>
                            </el-row>

                            <el-tabs v-model="activeTab1">

                                <el-tab-pane :label="$t('models.request.request_details')" name="request_details">
                                    <el-form-item :label="$t('models.request.prop_title')" :rules="validationRules.title"
                                                  prop="title">
                                        <el-input :disabled="$can($permissions.update.serviceRequest)" type="text"
                                                  v-model="model.title"/>
                                    </el-form-item>
                                    <el-form-item :label="$t('models.request.description')" :rules="validationRules.description"
                                                  prop="description">
                                        <el-input
                                            :autosize="{minRows: 16}"
                                            :disabled="$can($permissions.update.serviceRequest)"
                                            type="textarea"
                                            v-model="model.description">
                                        </el-input>
                                    </el-form-item>
                                </el-tab-pane>

                                <el-tab-pane name="request_images">
                                    <span slot="label">
                                        <el-badge :value="mediaCount" :max="99" class="admin-layout">{{ $t('models.request.images') }}</el-badge>
                                    </span>
                                    <div slot="header">
                                        <p class="comments-header">{{$t('models.request.images')}}</p>
                                    </div>
                                    <el-alert
                                        v-if="!media.length || (!model.media && !model.media.length)"
                                        :title="$t('models.request.no_images_message')"
                                        type="info"
                                        show-icon
                                        :closable="false"
                                    >
                                    </el-alert>
                                    <upload-document
                                        @fileUploaded="uploadFiles"
                                        class="drag-custom mt15"
                                        drag
                                        multiple
                                    />
                                    <div class="mt15">
                                        <request-media :data="[...model.media, ...media]" @deleteMedia="deleteMedia"
                                                       v-if="media.length || (model.media && model.media.length)"></request-media>
                                    </div>
                                </el-tab-pane>

                            </el-tabs>

                            <!--                            <el-form-item-->
                            <!--                                :label="$t('models.request.is_public')"-->
                            <!--                                class="switch-item"-->
                            <!--                                prop="is_public"-->
                            <!--                                style=""-->
                            <!--                            >-->
                            <!--                                <el-switch-->
                            <!--                                    :disabled="$can($permissions.update.serviceRequest)"-->
                            <!--                                    style="margin-left: 5px;"-->
                            <!--                                    v-model="model.is_public"-->
                            <!--                                >-->
                            <!--                                </el-switch>-->
                            <!--                            </el-form-item>-->
                            <!--                            <small>{{$t('models.request.public_legend')}}</small>-->
                        </card>
                        <template v-if="$can($permissions.update.serviceRequest)">
                            <card class="mt15" v-if="model.id">
                                <div slot="header">
                                    <p class="comments-header">{{$t('models.request.conversation')}}</p>
                                </div>
                                <el-table
                                    :data="conversations"
                                    style="width: 100%">
                                    <el-table-column
                                        :label="$t('models.user.name')"
                                        prop="user.name"
                                    >
                                    </el-table-column>
                                    <el-table-column
                                        width="100px"
                                    >
                                        <template slot-scope="scope">
                                            <el-button @click="openConversation(scope.row)" size="mini" type="primary">
                                                {{$t('models.request.open_conversation')}}
                                            </el-button>
                                        </template>
                                    </el-table-column>
                                </el-table>
                            </card>
                            <el-dialog
                                :visible.sync="conversationVisible"
                                width="50%">
                                <chat :id="selectedConversation.id" type="conversation"
                                      v-if="selectedConversation.id" show-templates />
                            </el-dialog>
                        </template>

                    </el-col>
                    <el-col :md="12">
                        <template v-if="$can($permissions.assign.request)">
                            <card :loading="loading">
                                <el-row :gutter="10">
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.request.status.label')"
                                                      :rules="validationRules.status"
                                                      prop="status">
                                            <el-select :placeholder="$t('models.request.placeholders.status')"
                                                       class="custom-select"
                                                       v-model="model.status">
                                                <el-option
                                                    :key="k"
                                                    :label="$t(`models.request.status.${status}`)"
                                                    :value="parseInt(k)"
                                                    v-for="(status, k) in constants.service_requests.status">
                                                </el-option>
                                            </el-select>
                                        </el-form-item>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-form-item :label="$t('models.request.due_date')"
                                                      :rules="validationRules.due_date">
                                            <el-date-picker
                                                :disabled="$can($permissions.update.serviceRequest)"
                                                :placeholder="$t('models.request.placeholders.due_date')"
                                                format="dd.MM.yyyy"
                                                style="width: 100%"
                                                type="date"
                                                v-model="model.due_date"
                                                value-format="yyyy-MM-dd"
                                            >
                                            </el-date-picker>
                                        </el-form-item>
                                    </el-col>
                                </el-row>
                            </card>
                            <card class="mt15" :loading="loading">
                                <el-row :gutter="20">
                                    <el-col :lg="6">
                                        <el-select @change="resetToAssignList"
                                                   class="custom-select"
                                                   v-model="assignmentType"
                                        >
                                            <el-option
                                                :key="type"
                                                :label="$t(`models.request.assignmentTypes.${type}`)"
                                                :value="type"
                                                v-for="(type) in assignmentTypes">
                                            </el-option>
                                        </el-select>
                                    </el-col>
                                    <el-col :lg="12" :xl="14">
                                        <el-select
                                            :loading="remoteLoading"
                                            :placeholder="$t('models.request.placeholders.search')"
                                            :remote-method="remoteSearchAssignees"
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
                                                :key="service.id"
                                                :label="service.name"
                                                :value="service.id"
                                                v-for="service in toAssignList"/>
                                        </el-select>
                                    </el-col>
                                    <el-col :lg="6" :xl="4">
                                        <el-button :disabled="!toAssign" @click="assignUser" class="full-button"
                                                   icon="ti-save" type="primary">
                                            {{$t('models.request.assign')}}
                                        </el-button>
                                    </el-col>
                                </el-row>
                                <relation-list
                                    :actions="assigneesActions"
                                    :columns="assigneesColumns"
                                    :filterValue="model.id"
                                    fetchAction="getAssignees"
                                    filter="request_id"
                                    ref="assigneesList"
                                    v-if="model.id"
                                />
                            </card>
                        </template>
                        <!--                    v-if="(!$can($permissions.update.serviceRequest)) || ($can($permissions.update.serviceRequest) && (media.length || (model.media && model.media.length)))"-->
                        <card class="mt15" v-if="model.id">
                            <el-tabs v-model="activeTab2">
                                <el-tab-pane :label="$t('models.request.comments')" name="comments">
                                    <chat :id="model.id" type="request"/>
                                </el-tab-pane>
                                <el-tab-pane>
                                    <span slot="label">
                                        <el-badge value="0" :max="99" class="admin-layout">{{ $t('models.request.internal_notices') }}</el-badge>
                                    </span>
                                </el-tab-pane>
                            </el-tabs>
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
        <ServiceDialog
            :address="address"
            :conversations="conversations"
            :mailSending="mailSending"
            :managers="model.assignees"
            :providers="model.providers"
            :selectedServiceRequest="selectedServiceRequest"
            :showServiceMailModal="showServiceMailModal"
            :requestData="selectedRequestData"
            @close="closeMailModal"
            @send="sendServiceMail"
            v-if="(model.providers && model.providers.length) || (model.assignees && model.assignees.length)"
        />

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RequestsMixin from 'mixins/adminRequestsMixin';
    import ServiceModalMixin from 'mixins/adminServiceModalMixin';
    import Chat from 'components/Chat2';
    import {mapActions} from 'vuex';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import ServiceDialog from 'components/ServiceAttachModal';
    import {displaySuccess} from "../../../helpers/messages";
    import {Avatar} from 'vue-avatar'


    export default {
        name: 'AdminRequestsEdit',
        mixins: [RequestsMixin({
            mode: 'edit'
        }), ServiceModalMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            Chat,
            ServiceDialog,
            RelationList,
            EditActions,
            Avatar
        },
        data() {
            return {
                activeTab1: 'request_details',
                activeTab2: 'comments',
                conversationVisible: false,
                selectedConversation: {},
                constants: this.$store.getters['application/constants'],
                assigneesColumns: [{
                    prop: 'name',
                    label: this.$t('models.propertyManager.name')
                }, {
                    prop: 'type',
                    label: this.$t('models.request.userType.label'),
                    i18n: this.translateType
                }],
                assigneesActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.request.notify'),
                        tooltipMode: true,
                        type: 'success',
                        icon: 'el-icon-message',
                        onClick: this.openNotifyProvider
                    }, {
                        title: this.$t('models.request.unassign'),
                        tooltipMode: true,
                        type: 'danger',
                        icon: 'el-icon-close',
                        onClick: this.notifyUnassignment
                    }]
                }]
            }
        },
        computed: {
            visibilities() {
                if (this.model.tenant && this.model.tenant.building && this.model.tenant.building.district_id) {
                    return this.constants.serviceRequests.visibility;
                } else {
                    return Object.keys(this.constants.serviceRequests.visibility)
                        .filter(key => key != 3)
                        .reduce((obj, key) => {
                            obj[key] = this.constants.serviceRequests.visibility[key];
                            return obj;
                        }, {});
                }
            },
            selectedRequestData() {      
                return {             
                    tenant: this.model.tenant,
                    service_request_format: this.model.service_request_format,
                    category: (this.model.category.parent_id == null)? this.model.category.name : this.model.category.parentCategory.name + " > " + this.model.category.name
                }                
            },
            mediaCount() {
                if(this.model.media) {
                    return this.model.media.length;
                } else {
                    return 0;
                }
            }
        },
        methods: {
            ...mapActions(['unassignProvider', 'unassignManager', 'deleteRequest']),
            translateType(type) {
                return this.$t(`models.request.userType.${type}`);
            },
            isDisabled(status) {
                return _.indexOf(this.constants.service_requests.statusByAgent[this.model.status], parseInt(status)) < 0
            },
            notifyUnassignment(provider) {
                this.$confirm(this.$t(`models.request.confirmUnassign.title`), this.$t('models.request.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.request.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.request.confirmUnassign.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;
                        let resp;

                        const payload = {
                            request: this.model.id,
                            toAssignId: provider.id
                        };

                        if (provider.uType == 1) {
                            resp = await this.unassignProvider(payload)
                        } else {
                            resp = await this.unassignManager(payload)
                        }

                        if (resp && resp.data) {
                            await this.fetchCurrentRequest();
                            this.$refs.assigneesList.fetch();
                            const detachedType = provider.uType === 1 ? 'service' : 'manager';
                            displaySuccess({
                                success: true,
                                message: this.$t(`models.request.detached.${detachedType}`)
                            })
                        }
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            openNotifyProvider(provider) {            
                this.selectedServiceRequest = provider;
                this.showServiceMailModal = true;
            },
            openConversation(row) {
                this.selectedConversation = {};
                this.$nextTick(() => {
                    this.selectedConversation = row;
                    this.conversationVisible = true;
                })
            },            
        }
    };
</script>

<style lang="scss" scoped>
    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }

    .custom-select {
        display: block;
    }

    .tenant-link {
        display: flex;
        align-items: center;
        color: #6AC06F;
        text-decoration: none;

        & > span {
            margin-left: 5px;
        }
    }

</style>

<style lang="scss">
    .switch-item {
        display: flex;
        margin: 0;
        padding: 0;

        .el-form-item__label, .el-form-item__content {
            line-height: 20px;
        }
    }

    .admin-layout .el-badge__content.is-fixed {
        top: 19px;
        right: -5px;
        background-color: #6AC06F;
        margin-left: 5px;
        height: 18px;
        width: 6px;
    }

</style>
