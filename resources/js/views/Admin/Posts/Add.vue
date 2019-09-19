<template>
    <div class="units-add">
        <heading :title="$t('models.post.add')" icon="ti-annoucements" shadow="heavy" style="margin-bottom: 20px;">
            <add-actions :saveAction="submit" route="adminPosts" editRoute="adminPostsEdit"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12">
                    <card :loading="loading" class="mb20">
                        <el-row :gutter="20">
                            <el-col :lg="model.pinned? 12 : 8">
                                <el-form-item :label="$t('models.post.type.label')">
                                    <el-select style="display: block" v-model="model.pinned" @change="chagePinned">
                                        <el-option
                                            :label="$t(`models.post.type.article`)"
                                            :value="false"
                                        >
                                        </el-option>
                                        <el-option
                                            :label="$t(`models.post.type.pinned`)"
                                            :value="true"
                                        >
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="8" v-if="!model.pinned">
                                <el-form-item :label="$t('models.post.status.label')">
                                    <el-select style="display: block" v-model="model.status">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.post.status.${status}`)"
                                            :value="parseInt(key)"
                                            v-for="(status, key) in postConstants.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12" v-if="model.pinned">
                                <el-form-item :label="$t('models.post.category.label')">
                                    <el-select style="display: block" v-model="model.category">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.post.category.${category}`)"
                                            :value="parseInt(key)"
                                            v-for="(category, key) in postConstants.category">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="8" v-else>
                                <el-form-item :label="$t('models.post.visibility.label')">
                                    <el-select style="display: block" v-model="model.visibility">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.post.visibility.${visibility}`)"
                                            :value="parseInt(key)"
                                            v-for="(visibility, key) in postConstants.visibility">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <template v-if="model.pinned">
                            <el-form-item :label="$t('models.post.title_label')" :rules="validationRules.title"
                                          prop="title">
                                <el-input type="text" v-model="model.title"></el-input>
                            </el-form-item>
                        </template>
                        <el-form-item :label="$t('models.post.content')" :rules="validationRules.content"
                                      prop="content">
                            <el-input
                                :autosize="{minRows: 5}"
                                type="textarea"
                                v-model="model.content">
                            </el-input>
                        </el-form-item>
                        <el-form-item :label="$t('models.post.images')"
                        >
                            <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                            <div class="mt15">
                                <media :data="mediaFiles" @deleteMedia="deleteMedia"
                                       v-if="media.length || (model.media && model.media.length)"></media>
                            </div>
                        </el-form-item>

                    </card>


                </el-col>
                <el-col :md="12">
                    <card :loading="loading" class="mb20">
                        <el-row :gutter="10">
                            <el-col :lg="6">
                                <el-select @change="resetToAssignList"
                                           class="custom-select"
                                           v-model="assignmentType"
                                >
                                    <el-option
                                        :key="type"
                                        :label="$t(`models.post.assignmentTypes.${type}`)"
                                        :value="type"
                                        v-for="(type) in assignmentTypes">
                                    </el-option>
                                </el-select>
                            </el-col>
                            <el-col :lg="18" :xl="18">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.post.placeholders.search')"
                                    :remote-method="remoteSearchBuildings"
                                    class="custom-remote-select"
                                    filterable
                                    remote
                                    reserve-keyword
                                    style="width: 100%;"
                                    v-model="toAssign"
                                >
                                    <div class="custom-prefix-wrapper" slot="prefix">
                                        <i class="el-icon-search custom-icon"></i>
                                    </div>
                                    <el-option
                                        :key="building.id"
                                        :label="building.name"
                                        :value="building.id"
                                        v-for="building in toAssignList"/>
                                </el-select>
                            </el-col>
                        </el-row>
                    </card>
                    <template v-if="model.pinned">

                        <card :loading="loading" class="mt15">
                            <el-row :gutter="10">
                                <el-col :lg="24" :xl="24">
                                    <el-select
                                        :loading="remoteLoading"
                                        :placeholder="$t('models.post.placeholders.search_provider')"
                                        :remote-method="remoteSearchProviders"
                                        class="custom-remote-select"
                                        filterable
                                        remote
                                        reserve-keyword
                                        style="width: 100%;"
                                        v-model="toAssignProvider"
                                    >
                                        <div class="custom-prefix-wrapper" slot="prefix">
                                            <i class="el-icon-search custom-icon"></i>
                                        </div>
                                        <el-option
                                            :key="provider.id"
                                            :label="provider.name"
                                            :value="provider.id"
                                            v-for="provider in toAssignProviderList"/>
                                    </el-select>
                                </el-col>
                            </el-row>
                        </card>

                        <card :loading="loading" class="mt15">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.post.execution_interval.start')"
                                                  prop="execution_start">
                                        <el-date-picker
                                            :picker-options="{disabledDate: disabledExecutionStart}"
                                            format="dd.MM.yyyy HH:mm"
                                            style="width: 100%"
                                            type="datetime"
                                            v-model="model.execution_start"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.post.execution_interval.end')"
                                                  prop="execution_end">
                                        <el-date-picker
                                            :picker-options="{disabledDate: disabledExecutionEnd}"
                                            format="dd.MM.yyyy HH:mm"
                                            style="width: 100%"
                                            type="datetime"
                                            v-model="model.execution_end"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.post.pinned_to')" prop="pinned_to" :rules="validationRules.pinned_to">
                                        <el-date-picker
                                            format="dd.MM.yyyy HH:mm"
                                            style="width: 100%"
                                            type="datetime"
                                            v-model="model.pinned_to"
                                            value-format="yyyy-MM-dd HH:mm:ss"
                                        >
                                        </el-date-picker>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-form-item :label="$t('models.post.notify_email')" prop="notify_email"
                                          style="display: flex">
                                <el-switch style="margin-left: 10px" v-model="model.notify_email">
                                </el-switch>
                            </el-form-item>
                        </card>
                    </template>

                </el-col>
            </el-form>

        </el-row>
    </div>
</template>

<script>
    import PostsMixin from 'mixins/adminPostsMixin';
    import AddActions from 'components/EditViewActions';

    const mixin = PostsMixin({mode: 'add'});
    export default {
        mixins: [mixin],
        components: {
            AddActions
        },
        methods: {
            disabledExecutionStart(date) {
                const d = new Date(date).getTime();
                const executionEnd = new Date(this.model.execution_end).getTime();
                return executionEnd > 0 && d > executionEnd;
            },
            disabledExecutionEnd(date) {
                const d = new Date(date).getTime();
                const executionStart = new Date(this.model.execution_start).getTime();
                return d < executionStart;
            },
            chagePinned(nValue) {
                if(nValue) {
                    this.model.status = 2;
                }else {
                    this.model.status = 1;
                }
            }
        }
    }
</script>


<style scoped>
    .custom-select {
        display: block;
    }

    .custom-label {
        color: #6AC06F;
    }

    .mb20 {
        margin-bottom: 20px;
    }
</style>
