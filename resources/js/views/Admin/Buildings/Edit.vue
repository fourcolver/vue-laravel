<template>
    <div class="buildings-edit ">
        <heading :title="$t('models.building.edit_title')" icon="ti-home" shadow="heavy">
            <template>
                <div class="action-group">
                    <el-button @click="submit" size="small" type="primary" round> {{this.$t('actions.save')}}</el-button>
                    <el-button @click="saveAndClose" size="small" type="primary" round> {{this.$t('actions.saveAndClose')}}
                    </el-button>
                    <el-button @click="batchDeleteBuilding" size="small" type="danger" round icon="ti-trash"> {{this.$t('actions.delete')}}</el-button>
                    <el-button @click="goToListing" size="small" type="warning" round> {{this.$t('actions.close')}}
                    </el-button>
                </div>
            </template>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <el-tabs type="border-card" v-model="activeTab">
                    <el-tab-pane :label="$t('models.building.details')" name="details">
                        <el-form :model="model" label-position="top" label-width="192px" ref="form">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.name')" :rules="validationRules.name"
                                                  prop="name"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.name"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.district')" prop="district_id"
                                                  style="max-width: 512px;">
                                        <el-select
                                            :loading="remoteLoading"
                                            :placeholder="$t('models.building.placeholders.search')"
                                            :remote-method="remoteSearchDistricts"
                                            filterable
                                            remote
                                            reserve-keyword
                                            style="width: 100%;"
                                            v-model="model.district_id">
                                            <el-option
                                                :label="$t('none')"
                                                value=""
                                            />
                                            <el-option
                                                :key="district.id"
                                                :label="district.name"
                                                :value="district.id"
                                                v-for="district in districts"/>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.floor_nr')"
                                                  :rules="validationRules.floor_nr"
                                                  prop="floor_nr" style="max-width: 512px;">
                                        <el-input type="number" v-model="model.floor_nr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.address.street')" :rules="validationRules.street"
                                                  prop="street"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.street"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.building.house_nr')"
                                                  :rules="validationRules.street_nr"
                                                  prop="street_nr" style="max-width: 512px;">
                                        <el-input type="text" v-model="model.street_nr"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.address.zip')" :rules="validationRules.zip"
                                                  prop="zip"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.zip"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.address.city')" :rules="validationRules.city"
                                                  prop="city"
                                                  style="max-width: 512px;">
                                        <el-input type="text" v-model="model.city"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.address.state.label')"
                                                  :rules="validationRules.state_id"
                                                  prop="state_id" style="max-width: 512px;">
                                        <el-select :placeholder="$t('models.address.state.label')"
                                                   style="display: block"
                                                   v-model="model.state_id">
                                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                                       v-for="state in states"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.building.files')" name="files">
                        <draggable @sort="sortFiles" v-model="model.media">
                            <transition-group name="list-complete">
                                <el-row :gutter="10" :key="element.name" class="list-complete-item"
                                        v-for="(element, i) in model.media">
                                    <el-col :md="12">
                                        <strong>{{$t(`models.building.${element.collection_name}`)}}</strong>
                                    </el-col>
                                    <el-col :md="11">
                                        <a :href="element.url" class="file-name" target="_blank">
                                            {{element.name}}
                                        </a>
                                    </el-col>
                                    <el-col :md="1" style="text-align: right">
                                        <el-button :style="{color: 'red'}" @click="deleteDocument('media', i)"
                                                   icon="ti-close" size="mini" type="text"
                                        />
                                    </el-col>
                                </el-row>
                            </transition-group>
                        </draggable>
                        <div class="mt15">
                            <label class="card-label">{{$t('models.building.add_files')}}</label>
                            <el-select :placeholder="$t('models.building.select_media_category')"
                                       class="category-select"
                                       v-model="selectedFileCategory">
                                <el-option
                                    :key="item.value"
                                    :label="item.label"
                                    :value="item.value"
                                    v-for="item in fileCategories">
                                </el-option>
                            </el-select>
                            <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple
                                             v-if="selectedFileCategory"/>
                        </div>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.building.companies')" name="companies">
                        <div v-if="model.service_providers && model.service_providers.length">
                            <el-row :gutter="10" :key="service.id" class="list-complete-item"
                                    v-for="service in model.service_providers">
                                <el-col :md="7">
                                    <strong>{{$t(`models.service.${service.category}`)}}</strong>
                                </el-col>
                                <el-col :md="16">
                                    {{service.name}}
                                </el-col>
                                <el-col :md="1" style="text-align: right">
                                    <el-button :style="{color: 'red'}" @click="removeService(service)"
                                               icon="ti-close" size="mini"
                                               type="text"
                                    />
                                </el-col>
                            </el-row>
                        </div>
                        <div v-else>
                            {{$t('models.building.no_services')}}
                        </div>
                        <div class="mt15">
                            <label class="">{{$t('models.building.add_companies')}}</label>
                            <el-select multiple
                                       placeholder="Select"
                                       style="margin: 15px 0;width: 100%" v-model="model.service_providers_ids">
                                <el-option-group
                                    :key="serviceCategory"
                                    :label="$t(`models.service.${serviceCategory}`)"
                                    v-for="(services, serviceCategory) in allServices">
                                    <el-option
                                        :key="service.id"
                                        :label="service.name"
                                        :value="service.id"
                                        v-for="service in services">
                                    </el-option>
                                </el-option-group>
                            </el-select>
                        </div>
                    </el-tab-pane>

                    <el-tab-pane :label="$t('models.building.requests')" name="requests">
                        <relation-list
                            :actions="requestActions"
                            :columns="requestColumns"
                            :filterValue="model.id"
                            fetchAction="getRequests"
                            filter="building_id"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                </el-tabs>
            </el-col>
            <el-col :md="12">
                <el-tabs type="border-card" v-model="activeRightTab">
                    <el-tab-pane :label="$t('models.building.tenants')" name="tenants" v-loading="loading.state">
                        <relation-list
                            :actions="tenantActions"
                            :columns="tenantColumns"
                            :filterValue="model.id"
                            fetchAction="getTenants"
                            filter="building_id"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.building.managers')" name="managers">
                        <el-row :gutter="20">
                            <el-col :md="18">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.propertyManager.placeholders.search')"
                                    :remote-method="remoteSearchManagers"
                                    class="custom-remote-select"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="toAssign"
                                >
                                    <div class="custom-prefix-wrapper" slot="prefix">
                                        <i class="el-icon-search custom-icon"></i>
                                    </div>
                                    <el-option
                                        :key="manager.id"
                                        :label="`${manager.first_name} ${manager.last_name}`"
                                        :value="manager.id"
                                        v-for="manager in toAssignList"/>
                                </el-select>
                            </el-col>
                            <el-col :md="6">
                                <el-button @click="assignManagers" type="primary" class="btn-assign">
                                    <i class="ti-save"></i> {{$t('models.building.assign')}}
                                </el-button>
                            </el-col>
                        </el-row>
                        <relation-list
                            :actions="managerActions"
                            :columns="managerColumns"
                            :filterValue="model.id"
                            fetchAction="getPropertyManagers"
                            filter="building_id"
                            ref="propertyManagersList"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                </el-tabs>
                <raw-grid-statistics-card :data="statistics.raw"/>
                <el-row :gutter="15" type="flex">
                    <el-col :span="12">
                        <circular-progress-statistics-card
                            :percentage="statistics.percentage.occupied_units"
                            :title="$t('models.building.occupied_units')"
                            color="#FFA400"/>
                    </el-col>
                    <el-col :span="12">
                        <circular-progress-statistics-card
                            :percentage="statistics.percentage.free_units"
                            :title="$t('models.building.free_units')"
                            color="#F9690E"/>
                    </el-col>
                </el-row>
            </el-col>
        </el-row>

        <DeleteBuildingModal 
            :deleteBuildingVisible="deleteBuildingVisible"
            :delBuildingStatus="delBuildingStatus"
            :closeModal="closeDeleteBuildModal"
            :deleteSelectedBuilding="deleteSelectedBuilding"
        />
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import BuildingsMixin from 'mixins/adminBuildingsMixin';
    import UploadDocument from 'components/UploadDocument';
    import draggable from 'vuedraggable';
    import RelationList from 'components/RelationListing';    
    import globalFunction from "helpers/globalFunction";
    import DeleteBuildingModal from 'components/DeleteBuildingModal';

    export default {
        mixins: [globalFunction, BuildingsMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            RawGridStatisticsCard,
            CircularProgressStatisticsCard,
            ColoredStatisticsCard,
            UploadDocument,
            draggable,
            RelationList,
            DeleteBuildingModal            
        },
        data() {
            return {
                fileCategories: [
                    {
                        label: this.$t('models.building.house_rules'),
                        value: 'house_rules'
                    },
                    {
                        label: this.$t('models.building.operating_instructions'),
                        value: 'operating_instructions'
                    }
                ],
                selectedFileCategory: 'house_rules',
                activeTab: 'details',
                activeRightTab: 'tenants',
                tenantColumns: [{
                    prop: 'name',
                    label: this.$t('models.tenant.name')
                }, {
                    prop: 'status',
                    i18n: this.tenantStatusLabel,
                    withBadge: this.tenantStatusBadge,
                    label: this.$t('models.tenant.status.label')
                }],
                tenantActions: [{
                    width: '90px',
                    buttons: [{
                        title: this.$t('models.tenant.edit'),
                        type: 'primary',
                        onClick: this.tenantEditView
                    }]
                }],
                managerColumns: [{
                    prop: 'name',
                    label: this.$t('models.propertyManager.name')
                }],
                managerActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.building.unassign_manager'),
                        type: 'danger',
                        onClick: this.unassignManager,
                        tooltipMode: true,
                        icon: 'el-icon-close'
                    }, {
                        title: this.$t('models.propertyManager.edit'),
                        type: 'primary',
                        onClick: this.managerEditView,
                        tooltipMode: true,
                        icon: 'el-icon-edit'
                    }]
                }],
                requestColumns: [{
                    prop: 'category.name',
                    label: this.$t('models.request.category')
                }, {
                    prop: 'status',
                    i18n: this.requestStatusLabel,
                    withBadge: this.requestStatusBadge,
                    label: this.$t('models.request.status.label')
                }],
                requestActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.request.edit'),
                        type: 'primary',
                        onClick: this.requestEditView
                    }]
                }],
                toAssignList: '',
                toAssign: [],
                remoteLoading: false,
                deleteBuildingVisible: false,
                delBuildingStatus: -1, // 0: unit, 1: request, 2: both
            };
        },
        methods: {
            ...mapActions([
                "uploadBuildingFile",
                "deleteBuildingFile",
                "deleteBuildingService",
                "getPropertyManagers",
                "batchAssignUsersToBuilding",
                "unassignBuildingManager",
                "deleteBuilding",
                'deleteBuildingWithIds', 
                'checkUnitRequestWidthIds'
            ]),
            unassignManager(manager) {
                this.$confirm(this.$t(`models.request.confirmUnassign.title`), this.$t('models.request.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.request.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.request.confirmUnassign.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        const resp = await this.unassignBuildingManager({
                            building_id: this.model.id,
                            id: manager.id
                        });

                        displaySuccess(resp);

                        this.$refs.propertyManagersList.fetch();

                    } catch (e) {
                        console.log(e)
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(() => {
                    this.loading.status = false;
                })

            },
            tenantEditView(row) {
                this.$router.push({
                    name: 'adminTenantsEdit',
                    params: {
                        id: row.id
                    }
                });
            },
            managerEditView(row) {
                this.$router.push({
                    name: 'adminPropertyManagersEdit',
                    params: {
                        id: row.id
                    }
                });
            },
            requestEditView(request) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: request.id
                    }
                })
            },
            requestStatusBadge(status) {                
                return this.getRequestStatusColor(status);
            },
            requestStatusLabel(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`)
            },
            insertDocument(prop, file) {
                file.order = this.model.media.length + 1;
                this.uploadBuildingFile({
                    id: this.model.id,
                    [`${prop}_upload`]: file.src
                }).then((resp) => {
                    displaySuccess(resp);
                    this.model.media.push(resp.media);
                }).catch((err) => {
                    displayError(err);
                });
            },
            deleteDocument(prop, index) {
                this.deleteBuildingFile({
                    id: this.model.id,
                    media_id: this.model[prop][index].id
                }).then((resp) => {
                    displaySuccess(resp);
                    this.model[prop].splice(index, 1);
                    this.setOrder(prop);
                }).catch((error) => {
                    displayError(error);
                })
            },
            tenantStatusBadge(status) {
                const colorObject = {
                    1: '#6AC06F',
                    2: '#F56C6C'
                };

                return colorObject[status];
            },
            tenantStatusLabel(status) {
                return this.$t(`models.tenant.status.${this.tenantStatusConstants[status]}`)
            },
            setOrder() {
                _.each(this.model.media, (file, i) => {
                    file.order = i + 1;
                });
                this.$forceUpdate();
            },
            sortFiles() {
                this.setOrder();
            },
            uploadFiles(file) {
                this.insertDocument(this.selectedFileCategory, file);
            },
            removeService(service) {
                this.deleteBuildingService({
                    building_id: this.$route.params.id,
                    id: service.id
                }).then((resp) => {
                    this.model.service_providers = this.model.service_providers.filter((provider) => {
                        return provider.id !== service.id;
                    });
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },

            async assignManagers() {
                try {
                    const resp = await this.batchAssignUsersToBuilding({
                        id: this.model.id,
                        managersIds: this.toAssign
                    });
                    displaySuccess({
                        success: true,
                        message: this.$t('models.building.managers_assigned')
                    });
                    this.resetToAssignList();
                    this.$refs.propertyManagersList.fetch();
                } catch (e) {
                    displayError({
                        success: false,
                        message: this.$t('models.building.managers_assign_failed')
                    });
                    this.resetToAssignList();
                }
            },

            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            resetToAssignList() {
                this.toAssignList = [];
                this.toAssign = [];
            },
             async batchDeleteBuilding() {
                try {              
                    const resp = await this.checkUnitRequestWidthIds({ids:[this.model.id]});                    
                    this.delBuildingStatus = resp.data;

                    if(this.delBuildingStatus == -1) {
                        this.$confirm('This action is irreversible. Please proceed with caution.', 'Are you sure?', {
                            type: 'warning'
                        }).then(() => {
                            this.deleteBuilding({id:this.model.id})
                                .then(r => {
                                    displaySuccess(r);
                                    this.goToListing();
                                })
                                .catch(err => displayError(err));                            
                        }).catch(() => {
                        });
                    }else {
                        this.deleteBuildingVisible = true;
                    }
                } catch(err) {
                    displayError(err);
                } finally {                    
                }
            },     
            async deleteSelectedBuilding(isUnits, isRequests) {
                try {
                    const resp = await this.deleteBuildingWithIds({
                        ids: [this.model.id],
                        is_units: isUnits,
                        is_requests: isRequests
                    });
                    this.deleteBuildingVisible = false;
                    displaySuccess(resp); 
                    this.goToListing();            
                } catch (err) {
                    displayError(err);
                } finally {
                }
            },
            closeDeleteBuildModal() {
                this.deleteBuildingVisible = false;
            },

            async saveAndClose() {
                try {
                    const resp = await this.submit();
                    if (resp) {
                        this.goToListing();
                    }
                } catch (e) {
                    console.log(e)
                }
            },
            goToListing() {
                return this.$router.push({
                    name: "adminBuildings",
                    query: this.queryParams
                })
            },
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            requestStatusConstants() {
                return this.constants.serviceRequests.status
            },
            tenantStatusConstants() {
                return this.constants.tenants.status
            }
        }
    }
</script>

<style lang="scss" scoped>
    .buildings-edit {
        .heading {
            margin-bottom: 20px;
        }

        > .el-row > .el-col:last-of-type:not(.custom-column) {
            /*min-width: 448px;*/
            /*max-width: 576px;*/

            :global(.el-card) {
                label {
                    margin-bottom: .5em;
                    display: block;
                }
            }

            > *:not(:last-of-type) {
                margin-bottom: 1em;
            }
        }
    }

    .list-complete-item {
        transition: all 1s;
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #eee;

        & > .el-col {
            border-left: 1px solid #eee;
            padding-top: 10px;
            min-height: 50px;
            padding-bottom: 10px;
            display: flex;
            align-items: center;

            &:last-child {
                border-right: 1px solid #eee;
                justify-content: center;
            }
        }

        &:last-child {
            border-bottom: 1px solid #eee;
        }
    }

    .list-complete-enter, .list-complete-leave-active {
        opacity: 0;
    }

    .card-label {
        display: block;
        margin-bottom: 15px;
    }

    .file-name {
        max-width: 75%;
        word-wrap: break-word;
        color: #333;
    }

    .category-select {
        margin-bottom: 30px;
        width: 100%;
    }

    .btn-assign {
        width: 100%;
    }
</style>
