<template>
    <div class="units-edit">
        <heading :title="$t('models.unit.edit')" icon="ti-user" style="margin-bottom: 20px;" shadow="heavy">
            <edit-actions :saveAction="submit" :deleteAction="deleteUnit" route="adminUnits"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.building')" :rules="validationRules.building_id"
                                              prop="building_id">
                                    <el-select
                                        :loading="remoteLoading"
                                        :placeholder="$t('models.unit.placeholders.search')"
                                        :remote-method="remoteSearchBuildings"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="model.building_id">
                                        <el-option
                                            :key="building.id"
                                            :label="building.name"
                                            :value="building.id"
                                            v-for="building in buildings"/>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.assigned_tenant')"
                                              :rules="validationRules.tenant_id"
                                              prop="tenant_id">
                                    <el-select
                                        :loading="remoteLoading"
                                        :placeholder="$t('models.unit.placeholders.search')"
                                        :remote-method="remoteSearchTenants"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="model.tenant_id">
                                        <el-option
                                            :key="tenant.id"
                                            :label="tenant.name"
                                            :value="tenant.id"
                                            v-for="tenant in tenants"/>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.name')" :rules="validationRules.name" prop="name">
                                    <el-input autocomplete="off" type="text" v-model="model.name"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.type.label')" :rules="validationRules.type"
                                              prop="type">
                                    <el-select :placeholder="$t('models.unit.type.label')" class="w100p"
                                               style="width: 100%;"
                                               v-model="model.type">
                                        <el-option :key="type.type" :label="$t('models.unit.type.' + type.label )"
                                                   :value="type.type"
                                                   v-for="type in unitTypes"></el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12" v-if="!isBusiness">
                                <el-form-item :label="$t('models.unit.room_no')" :rules="validationRules.room_no"
                                              prop="room_no"
                                >
                                    <el-select :placeholder="$t('models.unit.placeholders.select')" class="w100p"
                                               style="width: 100%;"
                                               v-model="model.room_no">
                                        <el-option :key="room.value"
                                                   :label="room.label"
                                                   :value="room.value"
                                                   v-for="room in rooms"/>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.monthly_rent')"
                                              :rules="validationRules.monthly_rent"
                                              prop="monthly_rent">
                                    <el-input autocomplete="off" step="0.01" type="number"
                                              v-model="model.monthly_rent"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.floor')" :rules="validationRules.floor" prop="floor">
                                    <el-input autocomplete="off" type="number" v-model="model.floor"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.unit.sq_meter')" prop="sq_meter">
                                    <el-input autocomplete="off" type="number" v-model="model.sq_meter"></el-input>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12" style="display: flex">
                                <el-form-item :label="$t('models.unit.basement')" :rules="validationRules.basement"
                                              class="switch-wrapper"
                                              style="margin-right: 20px;">
                                    <el-switch v-model="model.basement">
                                    </el-switch>
                                </el-form-item>
                                <el-form-item :label="$t('models.unit.attic')" :rules="validationRules.attic"
                                              class="switch-wrapper">
                                    <el-switch v-model="model.attic">
                                    </el-switch>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </card>
            </el-col>
            <el-col :md="12">
                <card :loading="loading">
                    <div slot="header">
                        <span>{{$t('models.unit.requests')}}</span>
                    </div>
                    <relation-list
                        :actions="requestActions"
                        :columns="requestColumns"
                        :filterValue="model.id"
                        fetchAction="getRequests"
                        filter="unit_id"
                        v-if="model.id"
                    />
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import EditActions from 'components/EditViewActions';
    import UnitsMixin from 'mixins/adminUnitsMixin';
    import RelationList from 'components/RelationListing';

    export default {
        mixins: [UnitsMixin({
            mode: 'edit',
            withRelation: true
        })],
        components: {
            Heading,
            Card,
            EditActions,
            RelationList
        },
        methods: {            
            ...mapActions([
                "deleteUnit"
            ]),
        }
    }
</script>
