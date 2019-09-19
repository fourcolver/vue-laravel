<template>
    <div class="units">
        <heading :title="title" icon="ti-house" shadow="heavy" v-if="building.name">
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
            @selectionChanged="selectionChanged"
            v-if="isReady"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions} from 'vuex';
    import {displayError} from "helpers/messages";
    import ListTableMixin from 'mixins/ListTableMixin';

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
        mixins: [mixin],
        data() {
            return {
                isReady: false,
                fetchParams: {},
                header: [{
                    label: this.$t('models.unit.name'),
                    prop: 'name'
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
            add() {
                this.$router.push({
                    name: 'adminBuildingUnitsAdd',
                    params: {
                        id: this.building.id
                    }
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminBuildingUnitsEdit',
                    params: {
                        id,
                        buildingId: this.building.id,
                    }
                });
            },
            ...mapActions(['getBuilding'])
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
            }
        },
        async created() {
            if (this.$route.params.building) {
                this.building = this.$route.params.building;
            } else {
                try {
                    this.loading = true;
                    this.building = await this.getBuilding({id: this.$route.params.id});
                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading = false;
                }
            }

            this.isReady = true;
            this.fetchParams.building_id = this.building.id;
        }
    }
</script>
