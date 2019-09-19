<template>
    <div class="tenants">
        <heading :title="$t('pages.tenant.title')" icon="ti-plus" shadow="heavy">
            <template v-if="$can($permissions.create.tenant)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">{{$t('models.tenant.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.tenant)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.tenant.delete')}}
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
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterStates from 'mixins/methods/getFilterStates';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';


    const mixin = ListTableMixin({
        actions: {
            get: 'getTenants',
            delete: 'deleteTenant'
        },
        getters: {
            items: 'tenants',
            pagination: 'tenantsMeta'
        }
    });

    export default {
        name: 'AdminTenants',
        mixins: [mixin, getFilterStates, getFilterDistricts],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: this.$t('models.tenant.id'),
                    prop: 'id',
                    width: 64
                }, {
                    label: this.$t('models.tenant.name'),
                    withMultipleProps: true,
                    props: ['name', 'birth_date']
                }, {
                    label: this.$t('models.tenant.contact_info_card'),
                    withMultipleProps: true,
                    props: ['user_email', 'private_phone']
                }, {
                    label: this.$t('models.tenant.building.name'),
                    withMultipleProps: true,
                    props: ['building_address_row', 'building_address_zip']
                }, {
                    label: this.$t('models.tenant.unit.name'),
                    withMultipleProps: true,
                    props: ['unit_name']
                }, {
                    label: this.$t('models.tenant.status.label'),
                    prop: 'status',
                    i18nPath: 'models.tenant.status',
                    class: 'rounded-select',
                    icon: true,
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "application/tenants",
                        onChange: this.listingSelectChangedNotify
                    }
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.tenant.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.tenant
                        ]
                    }]
                }]
            };
        },
        methods: {
            ...mapActions(['getBuildings', 'getUnits', 'changeTenantStatus']),
            add() {
                this.$router.push({
                    name: 'adminTenantsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminTenantsEdit',
                    params: {
                        id
                    }
                });
            },
            async getStateBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            async getBuildingUnits() {
                this.loading = true;
                const units = await this.getUnits({
                    get_all: true
                });
                this.loading = false;

                return units.data;
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`models.tenant.confirmChange.title`), this.$t('models.tenant.confirmChange.warning'), {
                    confirmButtonText: this.$t(`models.tenant.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`models.tenant.confirmChange.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;
                        const resp = await this.changeTenantStatus({id: row.id, status: row.status});
                        await this.fetchMore();
                        displaySuccess(resp);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading = false;
                    }
                }).catch(async () => {
                    this.loading = true;
                    await this.fetchMore();
                    this.loading = false;
                });
            },
            prepareFilters(property) {
                return Object.keys(this.tenantConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.tenant.${property}.${this.tenantConstants[property][id]}`)
                    };
                });
            },
            prepareRequestFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
        },
        computed: {
            ...mapState("application", {
                tenantConstants(state) {
                    return state.constants.tenants;
                },
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
                    }, {
                        name: this.$t('filters.states'),
                        type: 'select',
                        key: 'state_id',
                        data: [],
                        fetch: this.getFilterStates
                    }, {
                        name: this.$t('filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: [],
                        fetch: this.getStateBuildings
                    }, {
                        name: this.$t('filters.units'),
                        type: 'select',
                        key: 'unit_id',
                        data: [],
                        fetch: this.getBuildingUnits
                    }, {
                        name: this.$t('filters.districts'),
                        type: 'select',
                        key: 'district_id',
                        data: [],
                        fetch: this.getFilterDistricts
                    }, {
                        name: this.$t('filters.requestStatus'),
                        type: 'select',
                        key: 'request_status',
                        data: this.prepareRequestFilters("status")
                    }, {
                        name: this.$t('filters.status'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters('status'),
                    },
                ]
            }
        }
    };
</script>
