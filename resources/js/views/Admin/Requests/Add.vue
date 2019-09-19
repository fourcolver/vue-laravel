<template>
    <div class="services-edit">
        <heading :title="$t('models.request.add_title')" icon="ti-user" shadow="heavy">
            <add-actions :saveAction="submit" route="adminRequests" editRoute="adminRequestsEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-width="192px" ref="form" style="max-width: 512px;">
                        <el-form-item :label="$t('models.request.category')" :rules="validationRules.category"
                                      prop="category_id">
                            <el-select :placeholder="$t('models.request.placeholders.category')" class="custom-select"
                                       v-model="model.category_id">
                                <el-option
                                    :key="category.id"
                                    :label="category.name"
                                    :value="category.id"
                                    v-for="category in categories">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.qualification.label')"
                                      :rules="validationRules.qualification"
                                      prop="qualification"
                                      v-if="model.category_id && selectedCategoryHasQualification(model.category_id)">
                            <el-select :placeholder="$t('models.request.placeholders.qualification')"
                                       class="custom-select"
                                       v-model="model.qualification">
                                <el-option
                                    :key="k"
                                    :label="$t(`models.request.qualification.${qualification}`)"
                                    :value="parseInt(k)"
                                    v-for="(qualification, k) in $constants.service_requests.qualification">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.priority.label')" :rules="validationRules.priority"
                                      prop="priority">
                            <el-select :placeholder="$t('models.request.placeholders.priority')" class="custom-select"
                                       v-model="model.priority">
                                <el-option
                                    :key="k"
                                    :label="$t(`models.request.priority.${priority}`)"
                                    :value="parseInt(k)"
                                    v-for="(priority, k) in $constants.service_requests.priority">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.status.label')" :rules="validationRules.status"
                                      prop="status">
                            <el-select :placeholder="$t('models.request.placeholders.status')" class="custom-select"
                                       v-model="model.status">
                                <el-option
                                    :key="k"
                                    :label="$t(`models.request.status.${status}`)"
                                    :value="parseInt(k)"
                                    v-for="(status, k) in $constants.service_requests.status">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.visibility.label')"
                                      :rules="validationRules.visibility"
                                      prop="visibility"
                        >
                            <el-select
                                :placeholder="$t('models.request.placeholders.visibility')"
                                class="custom-select"
                                v-model="model.visibility">
                                <el-option
                                    :key="k"
                                    :label="$t(`models.request.visibility.${visibility}`)"
                                    :value="parseInt(k)"
                                    v-for="(visibility, k) in $constants.serviceRequests.visibility">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.due_date')" :rules="validationRules.due_date"
                                      prop="due_date">
                            <el-date-picker
                                :placeholder="$t('models.request.placeholders.due_date')"
                                format="dd.MM.yyyy"
                                style="width: 100%"
                                type="date"
                                v-model="model.due_date"
                                value-format="yyyy-MM-dd"
                            >
                            </el-date-picker>
                        </el-form-item>
                        <el-form-item v-if="model.tenant">
                            <label slot="label">
                                {{$t('models.request.tenant')}}
                            </label>
                            <router-link :to="{name: 'adminTenantsEdit', params: {id: model.tenant.id}}">
                                {{model.tenant.first_name}} {{model.tenant.last_name}}
                            </router-link>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.tenant')" :rules="validationRules.tenant_id"
                                      prop="tenant_id"
                                      v-else>
                            <el-select
                                :loading="remoteLoading"
                                :placeholder="$t('models.request.placeholders.tenant')"
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
                        <el-form-item :label="$t('models.request.prop_title')" :rules="validationRules.title"
                                      prop="title">
                            <el-input type="text" v-model="model.title"/>
                        </el-form-item>
                        <el-form-item :label="$t('models.request.description')" :rules="validationRules.description"
                                      prop="description">
                            <el-input
                                :autosize="{minRows: 5}"
                                type="textarea"
                                v-model="model.description">
                            </el-input>
                        </el-form-item>
                        <!--                        <el-form-item :label="$t('models.request.is_public')"-->
                        <!--                                      prop="is_public">-->
                        <!--                            <el-switch-->
                        <!--                                v-model="model.is_public"-->
                        <!--                            >-->
                        <!--                            </el-switch>-->
                        <!--                        </el-form-item>-->
                    </el-form>
                </card>
            </el-col>
            <el-col :md="12">
                <card>
                    <div slot="header">
                        <p class="comments-header">{{$t('models.request.images')}}</p>
                    </div>
                    <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                    <div class="mt15">
                        <request-media :data="media" @deleteMedia="deleteMedia" v-if="media.length"></request-media>
                    </div>
                </card>
            </el-col>
        </el-row>

    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RequestsMixin from 'mixins/adminRequestsMixin';
    import {displayError} from "helpers/messages";
    import AddActions from 'components/EditViewActions';


    export default {
        name: 'AdminRequestsEdit',
        mixins: [RequestsMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions
        },
        data() {
            return {
                couldSaveWithService: false
            }
        },
        methods: {
            async saveWithService(serviceAttachModel) {
                try {
                    const resp = await this.saveRequest();

                    if (resp.data.id) {
                        this.model.id = resp.data.id;
                        await this.sendServiceMail(serviceAttachModel);
                        this.setSelectedServiceRequest({});
                    }

                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading.state = false;
                }
            }
        },
        watch: {
            model: {
                deep: true,
                handler(newVal, oldVal) {
                    this.$refs.form.validate((valid) => {
                        if (!valid) {
                            this.couldSaveWithService = false;
                            return false;
                        }

                        this.couldSaveWithService = true;
                    })
                }
            }
        }
    };
</script>

<style lang="scss" scoped>
    .services-edit {
        .heading {
            margin-bottom: 20px;
        }
    }

    .custom-select {
        display: block;
    }


</style>

<style>
    .el-button > i {
        margin-right: 5px;
    }
</style>
