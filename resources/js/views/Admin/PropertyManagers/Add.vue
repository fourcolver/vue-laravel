<template>
    <div class="services-add">
        <heading :title="$t('models.propertyManager.add')" icon="ti-user" shadow="heavy">
            <add-actions :saveAction="submit" route="adminPropertyManagers" editRoute="adminPropertyManagersEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :md="12">
                        <card :loading="loading">
                            <el-form-item :label="$t('models.propertyManager.firstName')"
                                          :rules="validationRules.first_name"
                                          prop="first_name">
                                <el-input type="text" v-model="model.first_name"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.lastName')" :rules="validationRules.last_name"
                                          prop="last_name">
                                <el-input type="text" v-model="model.last_name"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.title')" :rules="validationRules.title"
                                          prop="title">
                                <el-select style="display: block" v-model="model.title">
                                    <el-option
                                        :key="title"
                                        :label="$t(`models.propertyManager.titles.${title}`)"
                                        :value="title"
                                        v-for="title in titles">
                                    </el-option>
                                </el-select>
                            </el-form-item>

                            <el-form-item :label="$t('models.propertyManager.profession')"
                                          :rules="validationRules.profession"
                                          prop="profession">
                                <el-input type="text" v-model="model.profession"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.slogan')" :rules="validationRules.slogan"
                                          prop="slogan">
                                <el-input type="text" v-model="model.slogan"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.linkedin_url')"
                                          :rules="validationRules.linkedin_url"
                                          prop="linkedin_url">
                                <el-input type="text" v-model="model.linkedin_url"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.xing_url')" :rules="validationRules.xing_url"
                                          prop="xing_url">
                                <el-input type="text" v-model="model.xing_url"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.propertyManager.phone')" prop="user.phone">
                                <el-input type="text" v-model="model.user.phone"/>
                            </el-form-item>
                            <el-form-item :label="$t('models.user.profile_image')">
                                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                            </el-form-item>

                            <el-form-item :rules="validationRules.email" label="Email" prop="user.email">
                                <el-input type="email" v-model="model.user.email"/>
                            </el-form-item>
                            <el-form-item :label="$t('password')" :rules="validationRules.password" autocomplete="off"
                                          prop="user.password">
                                <el-input type="password" v-model="model.user.password"/>
                            </el-form-item>
                            <el-form-item :label="$t('confirm_password')" :rules="validationRules.password_confirmation"
                                          prop="user.password_confirmation">
                                <el-input type="password" v-model="model.user.password_confirmation"/>
                            </el-form-item>
                        </card>
                    </el-col>
                    <el-col :md="12">
                        <card :loading="loading">
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.propertyManager.building_card')}}
                            </el-divider>

                            <div class="mt15">
                                <el-select
                                    :loading="loading.state"
                                    :placeholder="$t('models.propertyManager.buildings_search')"
                                    :remote-method="remoteSearchBuildings"
                                    class="mt15"
                                    filterable
                                    multiple
                                    remote
                                    reserve-keyword
                                    style="display: block; margin-bottom: 15px"
                                    v-model="model.buildings">
                                    <el-option
                                        :key="building.id"
                                        :label="building.name"
                                        :value="building.id"
                                        v-for="building in toAssignList">
                                    </el-option>
                                </el-select>
                            </div>
                        </card>
                    </el-col>
                </el-row>
            </el-form>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import PropertyManagersMixin from 'mixins/adminPropertyManagersMixin';
    import Cropper from 'components/Cropper';
    import AddActions from 'components/EditViewActions';

    export default {
        name: 'AdminPropertyManagersAdd',
        mixins: [PropertyManagersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            AddActions
        }
    }
</script>

<style lang="scss" scoped>
    .services-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>

