<template>
    <div class="units-add">
        <heading :title="title" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;"/>
        <div class="crud-view">
            <card :loading="loading">
                <el-form :model="model" label-width="192px" ref="form" style="max-width: 512px;">
                    <el-form-item :rules="validationRules.tenant_id" label="Assigned tenant" prop="tenant_id">
                        <el-select
                            :loading="remoteLoading"
                            :remote-method="remoteSearchTenants"
                            filterable
                            placeholder="Search..."
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
                    <el-form-item :label="$t('models.unit.name')" :rules="validationRules.name" prop="name">
                        <el-input autocomplete="off" type="text" v-model="model.name"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.type.label')" :rules="validationRules.type" prop="type">
                        <el-select :placeholder="$t('models.unit.type.label')" class="w100p" style="width: 100%;"
                                   v-model="model.type">
                            <el-option :key="type.type" :label="$t('models.unit.type.' + type.label )"
                                       :value="type.type"
                                       v-for="type in unitTypes"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.room_no')" :rules="validationRules.room_no" prop="room_no"
                                  v-if="!isBusiness">
                        <el-select class="w100p" placeholder="Select" style="width: 100%;" v-model="model.room_no">
                            <el-option :key="room.value"
                                       :label="room.label"
                                       :value="room.value"
                                       v-for="room in rooms"/>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.monthly_rent')" :rules="validationRules.monthly_rent"
                                  prop="monthly_rent">
                        <el-input autocomplete="off" step="0.01" type="number"
                                  v-model="model.monthly_rent"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.floor')" :rules="validationRules.floor" prop="floor">
                        <el-input autocomplete="off" type="number" v-model="model.floor"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.basement')" :rules="validationRules.basement"
                                  class="switch-wrapper">
                        <el-switch v-model="model.basement">
                        </el-switch>
                    </el-form-item>
                    <el-form-item :label="$t('models.unit.attic')" :rules="validationRules.attic"
                                  class="switch-wrapper">
                        <el-switch v-model="model.attic">
                        </el-switch>
                    </el-form-item>
                    <el-form-item>
                        <el-button @click="submit" icon="ti-save" type="primary">
                            {{$t('models.user.save')}}
                        </el-button>
                    </el-form-item>
                </el-form>
            </card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import UnitsMixin from 'mixins/adminUnitsMixin';

    export default {
        mixins: [UnitsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card
        }
    }
</script>
