<template>
    <div class="districts">
        <heading :title="$t('models.district.title')" icon="ti-house" shadow="heavy">
            <template v-if="$can($permissions.create.district)">
                <el-button @click="openAdd" icon="ti-plus" round size="small" type="primary">
                    {{$t('models.district.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.district)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.district.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :fetchMoreParams="fetchParams"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="items"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
            v-if="isReady">
        </list-table>
        <el-dialog :title="modalText.title" :visible.sync="showModal">
            <el-tabs v-model="activeTab">
                <el-tab-pane :label="$t('models.district.details')" name="details">
                    <el-form :model="model" ref="form">
                        <el-form-item :label="$t('models.district.name')" :rules="validationRules.name" prop="name">
                            <el-input autocomplete="off" v-model="model.name"></el-input>
                        </el-form-item>
                    </el-form>
                </el-tab-pane>
                <el-tab-pane :label="$t('models.district.buildings')" name="buildings" v-if="!_.isEmpty(model.buildings)">
                    <div>
                        <router-link :key="building.id" :to="{name: 'adminBuildingsEdit', params: {id: building.id}}"
                                     v-for="building in model.buildings">{{building.name}}
                        </router-link>
                    </div>
                </el-tab-pane>
            </el-tabs>
            <span class="dialog-footer" slot="footer">
                <el-button @click="showModal = false">{{ $t('models.district.cancel') }}</el-button>
                <el-button @click="submitForm" type="primary">{{ modalText.button }}</el-button>
            </span>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import ModalCrudMixin from 'mixins/modalCrudMixin';
    import {mapActions} from 'vuex';


    const mixin = ListTableMixin({
        actions: {
            get: 'getDistricts',
            delete: 'deleteDistrict'
        },
        getters: {
            items: 'districts',
            pagination: 'districtsMeta'
        }
    });

    const modalMixin = ModalCrudMixin({
        actions: {
            delete: 'deleteDistrict',
            get: 'getDistrict',
            update: 'updateDistrict',
            create: 'createDistrict'
        }
    });

    export default {
        components: {
            Heading
        },
        mixins: [mixin, modalMixin],
        data() {
            return {
                activeTab: 'details',
                i18nName: 'district',
                header: [{
                    label: this.$t('models.district.name'),
                    prop: 'name'
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.district.edit_action'),
                        onClick: this.openEditWithRelation,
                        permissions: [
                            this.$permissions.update.district
                        ]
                    }]
                }],
                model: {
                    id: '',
                    name: '',
                    description: '',
                    buildings: []
                },
                validationRules: {
                    name: [{
                        required: true,
                        message: this.$t('models.district.required')
                    }],
                }
            };
        },
        computed: {
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    }
                ]
            }
        },
        methods: {
            ...mapActions(['getBuildings']),
            async openEditWithRelation(district) {
                this.loading = true;
                const buildingsResp = await this.getBuildings({get_all: true, district_id: district.id});
                await this.openEdit(district);
                this.$set(this.model, 'buildings', buildingsResp.data);
                this.loading = false;
            }
        }
    }
</script>
