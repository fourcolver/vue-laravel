<template>
    <div class="services">
        <heading :title="$t('models.propertyManager.title')" icon="ti-user" shadow="heavy">
            <template v-if="$can($permissions.create.propertyManager)">            
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">
                    {{$t('models.propertyManager.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.propertyManager)">
                <el-button :disabled="!selectedItems.length" @click="openDeleteWithReassignment" icon="ti-trash" round
                           size="small"
                           type="danger">
                    {{$t('models.propertyManager.delete')}}
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
        <el-dialog  class="delete_width_reassign_modal" 
                    :close-on-click-modal="false" :title="$t('models.propertyManager.delete_with_reassign_modal.title')"
                    :visible.sync="assignManagersVisible"
                    v-loading="processAssignment" width="30%">
            <el-row>
                <el-col :span="24">
                    <p class="description">{{$t('models.propertyManager.delete_with_reassign_modal.description')}}</p>
                    <el-select
                        :loading="remoteLoading"
                        :placeholder="$t('models.propertyManager.placeholders.search')"
                        :remote-method="remoteSearchManagers"
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
                            :key="manager.id"
                            :label="`${manager.first_name} ${manager.last_name}`"
                            :value="manager.id"
                            v-for="manager in toAssignList"/>
                    </el-select>
                </el-col>
            </el-row>
            <el-row>
                <el-col :span="24">
                    <el-button 
                        :disabled="!toAssign"
                        @click="batchDelete(true)" 
                        size="mini" 
                        type="primary">
                        {{$t('models.propertyManager.delete_with_reassign_modal.title')}}
                    </el-button>
                </el-col>
            </el-row> 
            <span class="dialog-footer" slot="footer">
                <el-button @click="closeModal" size="mini">{{$t('models.building.cancel')}}</el-button>                
                <el-button @click="batchDelete(false)" size="mini" type="danger">{{$t('models.propertyManager.delete_without_reassign')}}</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import {displayError, displaySuccess} from "helpers/messages";
    import {mapActions} from 'vuex';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';

    const mixin = ListTableMixin({
        actions: {
            get: 'getPropertyManagers',
            delete: 'deletePropertyManager'
        },
        getters: {
            items: 'propertyManagers',
            pagination: 'propertyManagersMeta'
        }
    });

    export default {
        name: 'AdminPropertyManagers',
        mixins: [mixin, getFilterDistricts],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: this.$t('models.propertyManager.name'),
                    prop: 'name'
                }, {
                    label: this.$t('models.propertyManager.email'),
                    prop: 'user.email'
                }, {
                    label: this.$t('models.propertyManager.phone'),
                    prop: 'user.phone'
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.propertyManager.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.propertyManager
                        ]
                    }]
                }],
                assignManagersVisible: false,
                processAssignment: false,
                toAssignList: '',
                toAssign: '',
                remoteLoading: false
            }
        },
        computed: {
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('filters.districts'),
                        type: 'select',
                        key: 'district_id',
                        data: [],
                        fetch: this.getFilterDistricts
                    },
                    {
                        name: this.$t('filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: [],
                        fetch: this.getFilterBuildings
                    }
                ]
            }
        },
        methods: {
            ...mapActions(["remoteSearchManagers", "batchDeletePropertyManagers", "getBuildings", "getIDsAssignmentsCount"]),
            async getFilterBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            add() {
                this.$router.push({
                    name: 'adminPropertyManagersAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminPropertyManagersEdit',
                    params: {
                        id
                    }
                });
            },
            async openDeleteWithReassignment() {
                try {
                    const resp = await this.getIDsAssignmentsCount({
                        ids:_.map(this.selectedItems, 'id')
                    });

                    if(resp.data > 0) {
                        this.assignManagersVisible = true;
                    }else {
                        this.$confirm('Are you sure you want to delete?', 'Confirm?', {
                            confirmButtonText: 'OK',
                            cancelButtonText: 'Cancel',
                            type: 'warning',
                            roundButton: true
                        }).then(() => {
                            this.batchDelete(false);
                        }).catch(() => {
                        });
                    }
                } catch (e) {
                    displayError(e);
                }   
            },
            async batchDelete(withReassign) {
                try {                    
                    const resp = await this.batchDeletePropertyManagers({
                        managerIds: _.map(this.selectedItems, 'id'),
                        assignee: (this.toAssign && withReassign) ? this.toAssign : 0
                    });

                    if (resp) {
                        displaySuccess(resp);
                        this.closeModal();
                        this.fetchMore();
                    }
                } catch (e) {
                    displayError(e);
                }
            },
            closeModal() {
                this.assignManagersVisible = false;
                this.toAssign = '';
                this.toAssignList = [];
            },

            async remoteSearchManagers(search) {
                if (search === '') {
                    this.resetToAssignList();
                } else {
                    this.remoteLoading = true;

                    try {
                        const resp = await this.getPropertyManagers({
                            get_all: true,
                            search,
                            disableCommit: true
                        });

                        this.toAssignList = resp.data;
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.remoteLoading = false;
                    }
                }
            },
            
        }
    }
</script>

<style lang="scss" scoped>
    .delete_width_reassign_modal {
        .el-row {
            margin-bottom: 20px;
            &:last-child {
            margin-bottom: 0;
            }
        }

        .description {
            margin-top: 0;
        }
    }
</style>
