<template>
    <div class="buildings-add ">
        <heading :title="$t('models.building.add')" icon="ti-home" shadow="heavy">
            <add-actions :saveAction="submit" route="adminBuildings" editRoute="adminBuildingsEdit"/>
        </heading>
        <div class="crud-view">
            <card :loading="loading">
                <el-form :model="model" label-position="right" label-width="192px" ref="form">
                    <el-form-item :label="$t('models.building.name')" :rules="validationRules.name" prop="name"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.name"></el-input>
                    </el-form-item>
                    <!--<el-form-item prop="description" :label="$t('models.building.description')" :rules="validationRules.description" style="max-width: 512px;">-->
                    <!--<el-input type="textarea" v-model="model.description"></el-input>-->
                    <!--</el-form-item>-->
                    <el-form-item :label="$t('models.building.floor_nr')" :rules="validationRules.floor_nr" prop="floor_nr"
                                  style="max-width: 512px;">
                        <el-input type="number" v-model="model.floor_nr"></el-input>
                    </el-form-item>

                    <el-form-item :label="$t('models.address.street')" :rules="validationRules.street" prop="street"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.street"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.street_nr')" :rules="validationRules.street_nr"
                                  prop="street_nr" style="max-width: 512px;">
                        <el-input type="text" v-model="model.street_nr"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.zip')" :rules="validationRules.zip" prop="zip"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.zip"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.city')" :rules="validationRules.city" prop="city"
                                  style="max-width: 512px;">
                        <el-input type="text" v-model="model.city"></el-input>
                    </el-form-item>
                    <el-form-item :label="$t('models.address.state.label')" :rules="validationRules.state_id"
                                  prop="state_id" style="max-width: 512px;">
                        <el-select :placeholder="$t('models.address.state.label')" style="display: block"
                                   v-model="model.state_id">
                            <el-option :key="state.id" :label="state.name" :value="state.id"
                                       v-for="state in states"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item :label="$t('models.building.district')" prop="district_id" style="max-width: 512px;">
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
                                :key="district.id"
                                :label="district.name"
                                :value="district.id"
                                v-for="district in districts"/>
                        </el-select>
                    </el-form-item>
                </el-form>
            </card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import BuildingsMixin from 'mixins/adminBuildingsMixin';
    import AddActions from 'components/EditViewActions';

    export default {
        mixins: [BuildingsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions
        },
    }
</script>

<style lang="scss" scoped>
    .buildings-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>
