<template>
    <div class="units">
        <heading :title="$t('models.unit.title')" icon="ti-house" shadow="heavy">
            <template v-if="$can($permissions.create.unit)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">
                    {{$t('models.unit.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.unit)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.unit.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :fetchMoreParams="fetchParams"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
            v-if="isReady"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions} from 'vuex';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';
    import getFilterStates from "mixins/methods/getFilterStates";
    import getFilterPropertyManager from "mixins/methods/getFilterPropertyManager";


    const mixin = ListTableMixin({
        actions: {
            get: 'getUnits',
            delete: 'deleteUnit'
        },
        getters: {
            items: 'units',
            pagination: 'unitsMeta'
        }
    });

    export default {
        components: {
            Heading
        },
        mixins: [mixin, getFilterDistricts, getFilterStates, getFilterPropertyManager],
        data() {
            return {
                isReady: false,
                fetchParams: {},
                header: [{
                    label: this.$t('models.unit.name'),
                    prop: 'name'
                }, {
                    label: this.$t('models.unit.building'),
                    prop: 'building.name'
                }, {
                    label: this.$t('models.unit.type.label'),
                    prop: 'typeLabel'
                }, {
                    label: this.$t('models.unit.floor'),
                    prop: 'floor'
                }, {
                    label: this.$t('models.unit.room_no'),
                    prop: 'room_no'
                }, {
                    label: this.$t('models.unit.monthly_rent'),
                    prop: 'monthly_rent'
                }, {
                    label: this.$t('models.unit.tenant'),
                    withUsers: true,
                    prop: 'tenants'
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.unit.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.unit
                        ]
                    }]
                }],
                building: {}
            };
        },
        methods: {
            ...mapActions(['getBuildings']),
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
                    name: 'adminUnitsAdd',
                    params: {
                        id: this.building.id
                    }
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminUnitsEdit',
                    params: {
                        id,
                        buildingId: this.building.id,
                    }
                });
            }
        },
        computed: {
            title() {
                return `${this.building.name} - ${this.$t('menu.units')}`;
            },
            formattedItems() {
                return this.items.map((unit) => {
                    unit.typeLabel = this.$t(`models.unit.type.${unit.typeLabel}`);
                    return unit
                })
            },
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
                        name: this.$t('filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: [],
                        fetch: this.getFilterBuildings
                    },
                    {
                        name: this.$t('filters.propertyManagers'),
                        type: 'select',
                        key: 'manager_id',
                        data: [],
                        fetch: this.getFilterPropertyManagers
                    },
                    {
                        name: this.$t('filters.requests'),
                        type: 'select',
                        key: 'request',
                        data: [{
                            id: 1,
                            name: this.$t('filters.open_requests')
                        }]
                    },
                ]
            }
        },
        created() {
            this.isReady = true;
        }
    }
</script>
