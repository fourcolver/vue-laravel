<template>
    <div id="post-edit-view" class="units-edit mb20">
        <heading :title="$t('models.post.edit_title')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :saveAction="submit" :deleteAction="deletePost" route="adminPosts"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-form :model="model" label-position="top" label-width="192px" ref="form">
                <el-col :md="12">                
                    <el-card :loading="loading" class="mb20">
                        <el-row :gutter="20" class="mb20">
                            <el-col :lg="8">
                                <el-form-item :label="$t('models.post.type.label')">
                                    <el-select style="display: block" v-model="model.pinned">
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
                            <el-col :lg="8">
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
                            <el-col :lg="8" v-if="model.pinned">
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

                        <el-tabs v-if="model.pinned" v-model="activeTab1">
                            <el-tab-pane :label="$t('models.post.details_title')" name="details">
                                <el-form-item :label="$t('models.post.title_label')" :rules="validationRules.title"
                                            prop="title">
                                    <el-input type="text" v-model="model.title"></el-input>
                                </el-form-item>
                                <el-form-item :label="$t('models.post.content')" :rules="validationRules.content"
                                            prop="content">
                                    <el-input
                                        :autosize="{minRows: 5}"
                                        type="textarea"
                                        v-model="model.content">
                                    </el-input>
                                </el-form-item>
                                <el-form-item :label="$t('models.post.images')">
                                    <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                                    <div class="mt15" v-if="media.length || (model.media && model.media.length)">
                                        <media :data="mediaFiles" @deleteMedia="deleteMedia"
                                            v-if="media.length || (model.media && model.media.length)"></media>
                                    </div>
                                </el-form-item>
                            </el-tab-pane>
                            <el-tab-pane :label="$t('models.post.comments')" name="comments">
                                <chat class="edit-post-chat" :id="model.id" size="480px" type="post"/>
                            </el-tab-pane>
                        </el-tabs>
                        
                        <template v-if="!model.pinned">
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
                                <div class="mt15" v-if="media.length || (model.media && model.media.length)">
                                    <media :data="mediaFiles" @deleteMedia="deleteMedia"
                                        v-if="media.length || (model.media && model.media.length)"></media>
                                </div>
                            </el-form-item>
                        </template>                        
                    </el-card>

                    <el-card :loading="loading" v-if="!model.pinned && (!model.tenant)">
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
                            <el-col :lg="12" :xl="14">
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
                            <el-col :lg="6" :xl="4">
                                <el-button :disabled="!toAssign" @click="attachBuilding" class="full-button"
                                           icon="ti-save" type="primary">
                                    {{$t('models.post.assign')}}
                                </el-button>
                            </el-col>
                        </el-row>
                        <relation-list
                            :actions="assignmentsActions"
                            :columns="assignmentsColumns"
                            :filterValue="model.id"
                            fetchAction="getPostAssignments"
                            filter="post_id"
                            ref="assignmentsList"
                            v-if="model.id"
                        />

                    </el-card>

                    <template v-if="model.pinned">

                        <el-card :loading="loading" class="mt15">
                            <el-row :gutter="10">
                                <el-col :lg="18" :xl="20">
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
                                <el-col :lg="6" :xl="4">
                                    <el-button :disabled="!toAssignProvider" @click="attachProvider" class="full-button"
                                               icon="ti-save" type="primary">
                                        {{$t('models.post.assign')}}
                                    </el-button>
                                </el-col>
                            </el-row>
                            <relation-list
                                :actions="assignmentsProviderActions"
                                :columns="assignmentsProviderColumns"
                                :filterValue="model.id"
                                fetchAction="getServices"
                                filter="post_id"
                                ref="assignmentsProviderList"
                                v-if="model.id"
                            />
                        </el-card>

                        <el-card :loading="loading" class="mt15">
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
                                    <el-form-item :label="$t('models.post.pinned_to')"
                                                  :rules="validationRules.pinned_to"
                                                  prop="pinned_to">
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
                        </el-card>
                    </template>                
                </el-col>
                <el-col :md="12">
                    <el-card :loading="loading" class="contact-info-card mb20">
                        <el-row  :gutter="30" class="contact-info-card-row">
                            <el-col class="contact-info-card-col" :md="8">
                                <span class="custom-label">{{$t('models.post.user')}}</span>
                                <br>
                                <span v-if="model.user">
                                    <router-link :to="{name: 'adminUsersEdit', params: {id: model.user.id}}" class="tenant-link">
                                        <avatar :size="30"
                                                :src="'/' + model.user.avatar"
                                                v-if="model.user.avatar"></avatar>
                                        <avatar :size="28"
                                                :username="model.user.first_name ? `${model.user.first_name} ${model.user.last_name}`: `${model.user.name}`"
                                                backgroundColor="rgb(205, 220, 57)"
                                                color="#fff"
                                                v-if="!model.user.avatar"></avatar>
                                        <span>{{model.user.name}}</span>
                                    </router-link>
                                </span>
                            </el-col>                            
                            <el-col class="contact-info-card-col" :md="8">
                                <span class="custom-label">{{$t('models.post.published_at')}}</span>
                                <br>
                                <span class="custom-value" v-if="model.published_at">
                                        {{this.formatDatetime(model.published_at)}}
                                    </span>
                                <span class="custom-value" v-else>-</span>
                            </el-col>
                            <el-col class="contact-info-card-col" :md="8">
                                <span class="custom-label">{{$t('models.post.comments')}}</span>
                                <br>
                                <span class="custom-value">
                                    {{model.comments_count}}
                                </span>
                            </el-col>
                        </el-row>     
                        <el-row  :gutter="30" class="contact-info-card-row">
                            <el-col class="contact-info-card-col" :md="8">
                                <span class="custom-label">{{$t('models.post.likes')}}</span>
                                <br>
                                <span class="custom-value">
                                    {{model.likes_count}}
                                </span>    
                            </el-col>
                            <el-col class="contact-info-card-col" :md="8"></el-col>
                            <el-col class="contact-info-card-col" :md="8"></el-col>
                        </el-row>                                                    
                    </el-card>
                    <el-card :loading="loading" v-if="model.pinned && (!model.tenant)">
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
                            <el-col :lg="12" :xl="14">
                                <el-select
                                    :loading="remoteLoading"
                                    :placeholder="$t('models.post.placeholders.search')"
                                    :remote-method  ="remoteSearchBuildings"
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
                            <el-col :lg="6" :xl="4">
                                <el-button :disabled="!toAssign" @click="attachBuilding" class="full-button"
                                           icon="ti-save" type="primary">
                                    {{$t('models.post.assign')}}
                                </el-button>
                            </el-col>
                        </el-row>
                        <relation-list
                            :actions="assignmentsActions"
                            :columns="assignmentsColumns"
                            :filterValue="model.id"
                            fetchAction="getPostAssignments"
                            filter="post_id"
                            ref="assignmentsList"
                            v-if="model.id"
                        />
                    </el-card>

                    <el-card class="mt15" v-if="model.id && !model.pinned">
                        <div slot="header">{{$t('models.post.comments')}}</div>
                        <chat class="edit-post-chat" :id="model.id" size="480px" type="post"/>
                    </el-card>
                </el-col>
            </el-form>
        </el-row>

    </div>
</template>

<script>
    import Chat from 'components/Chat2';
    import EditActions from 'components/EditViewActions';
    import PostsMixin from 'mixins/adminPostsMixin';
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import RelationList from 'components/RelationListing';
    import {displayError, displaySuccess} from "helpers/messages";
    import {mapActions} from 'vuex';
    import {Avatar} from 'vue-avatar'

    const mixin = PostsMixin({mode: 'edit'});

    export default {
        mixins: [mixin, FormatDateTimeMixin],
        components: {
            Chat,
            EditActions,
            RelationList,
            Avatar
        },
        data() {
            return {
                assignmentsColumns: [{
                    prop: 'name',
                    label: this.$t('models.district.name')
                }, {
                    prop: 'type',
                    label: this.$t('models.post.assignType'),
                    i18n: this.translateType
                }],
                assignmentsActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.post.unassign'),
                        type: 'danger',
                        onClick: this.notifyUnassignment
                    }]
                }],
                assignmentsProviderColumns: [{
                    prop: 'name',
                    label: this.$t('models.service.name')
                }],
                assignmentsProviderActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.post.unassign'),
                        type: 'danger',
                        onClick: this.notifyProviderUnassignment
                    }]
                }],
                activeTab1: "details"
            }
        },
        methods: {
            ...mapActions(['unassignPostBuilding', 'unassignPostDistrict', 'unassignPostProvider', 'deletePost']),
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
            notifyUnassignment(row) {
                this.$confirm(this.$t(`models.post.confirmUnassign.title`), this.$t('models.post.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.post.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.post.confirmUnassign.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassign(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
            async unassign(toUnassign) {
                let resp;
                if (toUnassign.aType == 1) {
                    resp = await this.unassignPostBuilding({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                } else {
                    resp = await this.unassignPostDistrict({
                        id: this.model.id,
                        toAssignId: toUnassign.id
                    })
                }

                if (resp) {
                    await this.fetchCurrentPost();
                    this.$refs.assignmentsList.fetch();

                    this.toAssign = '';

                    const type = toUnassign.aType == 1 ? 'building' : 'district';

                    displaySuccess({
                        success: true,
                        message: this.$t(`models.post.detached.${type}`)
                    })
                }
            },
            async unassignProvider(toUnassign) {
                const resp = await this.unassignPostProvider({
                    id: this.model.id,
                    toAssignId: toUnassign.id
                });

                await this.fetchCurrentPost();
                this.$refs.assignmentsProviderList.fetch();

                this.toAssignProvider = '';

                displaySuccess({
                    success: true,
                    message: this.$t(`models.post.detached.provider`)
                })
            },
            notifyProviderUnassignment(row) {
                this.$confirm(this.$t(`models.post.confirmUnassign.title`), this.$t('models.post.confirmUnassign.warning'), {
                    confirmButtonText: this.$t(`models.post.confirmUnassign.confirmBtnText`),
                    cancelButtonText: this.$t(`models.post.confirmUnassign.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading.status = true;

                        await this.unassignProvider(row);

                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading.status = false;
                    }
                }).catch(async () => {
                    this.loading.status = false;
                });
            },
        }
    }
</script>

<style lang="scss" scope>
    .custom-select {
        display: block;
    }

    .custom-label {
        color: #6AC06F;
        display: inline-block;
        margin-bottom: 10px;
    }

    .custom-value {        
        line-height: 28px;
    }

    .mb20 {
        margin-bottom: 20px;
    }
    
    .contact-info-card {
        .contact-info-card-row {
            display: flex;
            border-bottom: 1px solid #EBEEF5;
            margin-left: 0 !important;
            margin-right: 0 !important;
            &:first-child {
                .contact-info-card-col {
                    padding-top: 0;
                }
            }
            &:last-child {
                border-bottom: 0;
                .contact-info-card-col {
                    padding-bottom: 0;
                }
            }
            .contact-info-card-col {
                &:first-child {
                    padding-left: 0 !important;
                }
                &:last-child {
                    padding-right: 0 !important;
                }
            }
        }
        
        .contact-info-card-col {
            border-right: 1px solid #EBEEF5;
            min-height: 57px;
            padding-bottom: 10px;
            padding-top: 10px;
            &:last-child {
                border: none;
            }
        }
    }

    .tenant-link {
        display: flex;
        align-items: center;
        color: #6AC06F;
        text-decoration: none;

        & > span {
            margin-left: 5px;
        }
    }
</style>

<style>

    #post-edit-view .el-card__body .el-form-item:last-child {
        margin-bottom: 0;
    }

    .edit-post-chat .add-comment {
        margin-bottom: 0 !important;
    }
</style>    