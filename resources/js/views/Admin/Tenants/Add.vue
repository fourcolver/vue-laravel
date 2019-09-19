<template>
    <div class="tenants-add">
        <heading :title="$t('models.tenant.add')" icon="ti-user" shadow="heavy">
            <add-actions :saveAction="submit" route="adminTenants" editRoute="adminTenantsEdit"/>
        </heading>
        <div class="crud-view">
            <el-form :model="model" label-width="192px" ref="form">
                <el-row :gutter="20">
                    <el-col :lg="12" :sm="24">
                        <card :loading="loading">
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.personal_details_card')}}
                            </el-divider>
                            <el-form-item :label="$t('models.tenant.title')" :rules="validationRules.title"
                                          prop="title">
                                <el-select placeholder="Select" style="display: block" v-model="model.title">
                                    <el-option
                                        :key="title"
                                        :label="$t(`models.tenant.titles.${title}`)"
                                        :value="title"
                                        v-for="title in titles">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.company')" :rules="validationRules.company"
                                          prop="company"
                                          v-if="model.title === titles.company">
                                <el-input autocomplete="off" type="text" v-model="model.company"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.first_name')" :rules="validationRules.first_name"
                                          prop="first_name">
                                <el-input autocomplete="off" type="text" v-model="model.first_name"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.last_name')" :rules="validationRules.last_name"
                                          prop="last_name">
                                <el-input autocomplete="off" type="text" v-model="model.last_name"></el-input>
                            </el-form-item>

                            <el-form-item :label="$t('models.tenant.birth_date')" :rules="validationRules.birth_date"
                                          prop="birth_date">
                                <el-date-picker
                                    :placeholder="$t('models.tenant.birth_date')"
                                    format="dd.MM.yyyy"
                                    style="width: 100%;"
                                    type="date"
                                    v-model="model.birth_date"
                                    value-format="yyyy-MM-dd"/>
                            </el-form-item>
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.contact_info_card')}}
                            </el-divider>
                            <el-form-item :label="$t('email')" :rules="validationRules.email" prop="email">
                                <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.mobile_phone')" prop="mobile_phone">
                                <el-input autocomplete="off" type="text" v-model="model.mobile_phone"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.private_phone')" prop="private_phone">
                                <el-input autocomplete="off" type="text" v-model="model.private_phone"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.work_phone')" prop="work_phone">
                                <el-input autocomplete="off" type="text" v-model="model.work_phone"></el-input>
                            </el-form-item>
                        </card>
                    </el-col>
                    <el-col :lg="12" :sm="24">
                        <card :loading="loading">
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.account_info_card')}}
                            </el-divider>
                            <!--                            <el-form-item :label="$t('models.user.profile_image')">-->
                            <!--                                <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>-->
                            <!--                            </el-form-item>-->
                            <el-form-item :label="$t('password')" :rules="validationRules.password" prop="password">
                                <el-input autocomplete="off" type="password" v-model="model.password"></el-input>
                            </el-form-item>
                            <el-form-item :label="$t('confirm_password')" :rules="validationRules.password_confirmation"
                                          prop="password_confirmation">
                                <el-input autocomplete="off" type="password"
                                          v-model="model.password_confirmation"></el-input>
                            </el-form-item>
                        </card>
                        <card :loading="loading" class="mt15">
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.building_card')}}
                            </el-divider>
                            <el-form-item :label="$t('models.tenant.building.name')" prop="building_id">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.tenant.search_building')"
                                    :remote-method="remoteSearchBuildings"
                                    :rules="validationRules.building_id"
                                    @change="searchUnits"
                                    filterable
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="model.building_id">
                                    <el-option
                                        :label="`--${$t('models.tenant.no_building')}--`"
                                        value=""
                                    />
                                    <el-option
                                        :key="building.id"
                                        :label="building.name"
                                        :value="building.id"
                                        v-for="building in buildings"/>
                                </el-select>
                            </el-form-item>
                            <el-form-item :label="$t('models.tenant.unit.name')" prop="unit_id"
                                          v-if="model.building_id">
                                <el-select :placeholder="$t('models.tenant.search_unit')" style="display: block"
                                           v-model="model.unit_id">
                                    <el-option
                                        :key="unit.id"
                                        :label="unit.name"
                                        :value="unit.id"
                                        v-for="unit in units">
                                    </el-option>
                                </el-select>
                            </el-form-item>
                        </card>
                        <card class="mt15">
                            <el-form :model="model" label-width="192px" ref="form">
                                <el-divider class="column-divider" content-position="left">
                                    {{$t('models.tenant.rent_contract')}}
                                </el-divider>
                                <el-form-item :label="$t('models.tenant.rent_start')"
                                              prop="rent_start">
                                    <el-date-picker
                                        :picker-options="{disabledDate: disabledRentStart}"
                                        :placeholder="$t('models.tenant.rent_start')"
                                        format="dd.MM.yyyy"
                                        style="width: 100%;"
                                        type="date"
                                        v-model="model.rent_start"
                                        value-format="yyyy-MM-dd"/>
                                </el-form-item>
                                <el-form-item>
                                    <el-row :gutter="20" class="list-complete-item" justify="center"
                                            style="margin-bottom: 1em;"
                                            type="flex"
                                            v-if="!_.isEmpty(toUploadContract)">
                                        <el-col :span="20">
                                            <a :href="toUploadContract.url" target="_blank"><strong>{{
                                                toUploadContract.name }}</strong></a>
                                            <el-image :src="toUploadContract.url"
                                                      v-if="isFileImage(toUploadContract.raw)"/>
                                            <embed :src="toUploadContract.url" v-else/>
                                        </el-col>
                                        <el-col :span="4">
                                            <el-button @click="deleteToUploadContract" icon="ti-trash" size="mini"
                                                       type="danger"/>
                                        </el-col>
                                    </el-row>
                                    <upload-document @fileUploaded="contractToUpload" class="drag-custom" drag/>
                                </el-form-item>
                                <!--                                <el-form-item :label="$t('models.tenant.rent_end')"-->
                                <!--                                              prop="rent_end">-->
                                <!--                                    <el-date-picker-->
                                <!--                                        :picker-options="{disabledDate: disabledRentEnd}"-->
                                <!--                                        :placeholder="$t('models.tenant.rent_end')"-->
                                <!--                                        format="dd.MM.yyyy"-->
                                <!--                                        style="width: 100%;"-->
                                <!--                                        type="date"-->
                                <!--                                        v-model="model.rent_end"-->
                                <!--                                        value-format="yyyy-MM-dd"/>-->
                                <!--                                </el-form-item>-->
                            </el-form>
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
    import AdminTenantsMixin from 'mixins/adminTenantsMixin';
    import Cropper from 'components/Cropper';
    import UploadDocument from 'components/UploadDocument';
    import AddActions from 'components/EditViewActions';


    export default {
        mixins: [AdminTenantsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            Cropper,
            UploadDocument,
            AddActions
        },
        data() {
            return {
                toUploadContract: {}
            }
        },
        methods: {
            contractToUpload(file) {
                this.toUploadContract = {...file, url: URL.createObjectURL(file.raw)};
            },
            deleteToUploadContract() {
                this.toUploadContract = {};
            }
        }
    }
</script>

<style lang="scss" scoped>
    .tenants-add {
        .heading {
            margin-bottom: 20px;
        }
    }

    .group-name {
        width: 192px;
        text-align: right;
        padding-right: 10px;
        box-sizing: border-box;
        font-size: 16px;
        font-weight: bold;
        color: #6AC06F;
    }

    .mb15 {
        margin-bottom: 15px;
    }
</style>
