<template>
    <div class="buildings">
        <heading :title="$t('models.building.title')" icon="ti-home" shadow="heavy">
            <template v-if="$can($permissions.create.building)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">{{$t('models.building.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.assign.manager)">
                <el-button :disabled="!selectedItems.length" @click="batchAssignManagers" icon="ti-user" round
                           size="small"
                           type="info">
                    {{$t('models.building.assign_managers')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.building)">
                <el-button :disabled="!selectedItems.length" @click="batchDeleteBuilding" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.building.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="items"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :close-on-click-modal="false" :title="$t('models.building.assign_managers')"
                   :visible.sync="assignManagersVisible"
                   v-loading="processAssignment" width="30%">
            <el-form :model="managersForm">
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
            </el-form>
            <span class="dialog-footer" slot="footer">
                <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>
                <el-button @click="assignManagers" size="mini" type="primary">{{$t('models.building.assign_managers')}}</el-button>
            </span>
        </el-dialog>

        <DeleteBuildingModal 
            :deleteBuildingVisible="deleteBuildingVisible"
            :delBuildingStatus="delBuildingStatus"
            :closeModal="closeDeleteBuildModal"
            :deleteSelectedBuilding="deleteSelectedBuilding"
        />
    </div>
</template>

<script>
    import {mapState, mapActions} from 'vuex';
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterStates from 'mixins/methods/getFilterStates';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';
    import getFilterPropertyManager from 'mixins/methods/getFilterPropertyManager';
    import {displaySuccess, displayError} from "helpers/messages";
    import DeleteBuildingModal from 'components/DeleteBuildingModal';
    const mixin = ListTableMixin({
        actions: {
            get: 'getBuildings',
            delete: 'deleteBuilding'
        },
        getters: {
            items: 'buildings',
            pagination: 'buildingsMeta'
        }
    });

    export default {
        mixins: [mixin, getFilterStates, getFilterDistricts, getFilterPropertyManager],
        components: {
            Heading,
            DeleteBuildingModal
        },
        data() {
            return {
                assignManagersVisible: false,
                deleteBuildingVisible: false,
                processAssignment: false,
                managersForm: {},
                toAssignList: '',
                toAssign: [],
                remoteLoading: false,
                delBuildingStatus: -1, // 0: unit, 1: request, 2: both
                header: [{
                    label: this.$t('models.request.address'),
                    withMultipleProps: true,
                    width: '300px',
                    props: ['address_row', 'address_zip']
                }, {
                    label: this.$t('models.building.units'),
                    withMultipleProps: true,
                    withLinks: true,
                    width: '90px',
                    route: {
                        name: 'adminBuildingUnits',
                        paramsKeys: {
                            model: 'building',
                            props: ['id']
                        }
                    },
                    props: ['units_count']
                }, {
                    label: this.$t('models.building.tenants'),
                    withUsers: true,
                    count: 'tenants_count',
                    width: '150px',
                    prop: 'tenants_last'
                }, {
                    label: this.$t('models.building.managers'),
                    withUsers: true,
                    width: '150px',
                    prop: 'managers_last',
                    count: 'property_managers_count'
                }, {
                    label: this.$t('models.building.requests'),
                    withCounts: true,
                    counts: [
                        {
                            prop: 'requests_count',
                            background: '#aaa',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.total')
                        }, {
                            prop: 'requests_received_count',
                            background: '#bbb',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.received')
                        }, {
                            prop: 'requests_assigned_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.assigned')
                        }, {
                            prop: 'requests_in_processing_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.in_processing')
                        }, {
                            prop: 'requests_reactivated_count',
                            background: '#ebb563',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.reactivated')
                        }, {
                            prop: 'requests_done_count',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.done')
                        }, {
                            prop: 'requests_archived_count',
                            background: '#67C23A',
                            color: '#fff',
                            label: this.$t('models.building.requestStatuses.archived')
                        }
                    ]
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.service.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.building
                        ]
                    }]
                }]
            };
        },
        computed: {
            ...mapState("application", {
                requestConstants(state) {
                    return state.constants.service_requests;
                }
            }),
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: [],
                        fetch: this.getFilterStates
                    },
                    {
                        name: this.$t('filters.districts'),
                        type: 'select',
                        key: 'district_id',
                        data: [],
                        fetch: this.getFilterDistricts
                    },
                    {
                        name: this.$t('filters.propertyManagers'),
                        type: 'select',
                        key: 'manager_id',
                        data: [],
                        fetch: this.getFilterPropertyManagers
                    },
                    {
                        name: this.$t('filters.requestStatus'),
                        type: 'select',
                        key: 'request_status',
                        data: this.prepareFilters("status")
                    }
                ];
            }
        },
        methods: {
            ...mapActions(['getPropertyManagers', 'batchAssignUsersToBuilding', 'deleteBuildingWithIds', 'checkUnitRequestWidthIds']),
            prepareFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
            units(building) {
                this.$router.push({
                    name: 'adminBuildingUnits',
                    params: {
                        building,
                        id: building.id
                    }
                });
            },
            add() {
                this.$router.push({
                    name: 'adminBuildingsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminBuildingsEdit',
                    params: {
                        id
                    }
                });
            },
            batchAssignManagers() {
                this.assignManagersVisible = true;
            },
            closeModal() {
                this.assignManagersVisible = false;
                this.toAssign = [];
                this.toAssignList = [];
            },
            assignManagers() {
                const promises = this.selectedItems.map((building) => {
                    return this.batchAssignUsersToBuilding({
                        id: building.id,
                        managersIds: this.toAssign
                    })
                });

                Promise.all(promises).then((resp) => {
                    this.processAssignment = false;
                    this.closeModal();
                    this.fetchMore();
                    displaySuccess({
                        success: true,
                        message: this.$t('models.building.managers_assigned')
                    });
                }).catch((error) => {
                    this.processAssignment = false;
                    this.closeModal();
                    displayError({
                        success: false,
                        message: this.$t('models.building.managers_assign_failed')
                    });
                });
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
                    const resp = await this.checkUnitRequestWidthIds({ids:_.map(this.selectedItems, 'id')});                    
                    this.delBuildingStatus = resp.data;

                    if(this.delBuildingStatus == -1) {
                        this.$confirm('This action is irreversible. Please proceed with caution.', 'Are you sure?', {
                            type: 'warning'
                        }).then(() => {
                            Promise.all(this.selectedItems.map((item) => {
                                return this.deleteBuilding(item)
                                    .then(r => {
                                        displaySuccess(r);
                                    })
                                    .catch(err => displayError(err));
                            })).then(() => {
                                this.fetchMore();
                            })
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
                        ids: _.map(this.selectedItems, 'id'),
                        is_units: isUnits,
                        is_requests: isRequests
                    });
                    this.deleteBuildingVisible = false;
                    displaySuccess(resp);                    
                } catch (err) {
                    displayError(err);
                } finally {
                    this.fetchMore();
                }
            },
            closeDeleteBuildModal() {
                this.deleteBuildingVisible = false;
            },            
        }
    };
</script>