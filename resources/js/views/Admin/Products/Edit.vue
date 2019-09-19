<template>
    <div class="units-edit mb20">
        <heading :title="$t('models.product.edit_title')" icon="ti-user" shadow="heavy" style="margin-bottom: 20px;">
            <edit-actions :saveAction="submit" :deleteAction="deleteProduct" route="adminProducts"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-row :gutter="20">
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.product.status.label')">
                                    <el-select class="custom-select" v-model="model.status">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.product.status.${status}`)"
                                            :value="parseInt(key)"
                                            v-for="(status, key) in productConstants.status">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.product.type.label')">
                                    <el-select class="custom-select" v-model="model.type">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.product.type.${type}`)"
                                            :value="parseInt(key)"
                                            v-for="(type, key) in productConstants.type">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12">
                                <el-form-item :label="$t('models.product.visibility.label')">
                                    <el-select class="custom-select" v-model="model.visibility">
                                        <el-option
                                            :key="key"
                                            :label="$t(`models.product.visibility.${visibility}`)"
                                            :value="parseInt(key)"
                                            v-for="(visibility, key) in productConstants.visibility">
                                        </el-option>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                            <el-col :lg="12" v-if="model.type !== 4">
                                <el-form-item :label="$t('models.product.price')" :rules="validationRules.price"
                                              prop="price">
                                    <el-row :gutter="5">
                                        <el-col :span="16">
                                            <el-input type="text" v-model="price.integer" />
                                        </el-col>
                                        <el-col :span="1">.</el-col>
                                        <el-col :span="7">
                                            <el-input type="text" v-model="price.decimals" maxlength="2" />
                                        </el-col>
                                    </el-row>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col>
                                <el-form-item :label="$t('models.product.product_title')" :rules="validationRules.title"
                                              prop="title">
                                    <el-input type="text" v-model="model.title"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row>
                            <el-col>
                                <el-form-item :label="$t('models.product.content')" :rules="validationRules.content"
                                              prop="content">
                                    <el-input
                                        :autosize="{minRows: 5}"
                                        type="textarea"
                                        v-model="model.content">
                                    </el-input>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </card>
            </el-col>
            <el-col :md="12">
                <card :loading="loading" class="mb20 contact-info-card">
                    <el-row  :gutter="30" class="contact-info-card-row">
                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">{{$t('models.product.user')}}</span>
                            <br>
                            <span class="custom-value" v-if="model.user">
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
                            <span class="custom-label">{{$t('models.product.contact')}}</span>
                            <br>
                            <span>
                                {{model.contact}}
                                </span>
                        </el-col>

                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">{{$t('models.product.published_at')}}</span>
                            <br>
                            <span class="custom-value" v-if="model.published_at">
                                    {{model.published_at}}
                                </span>
                            <span v-else>-</span>
                        </el-col>
                        
                        <!-- <el-col :md="12">
                            <span class="custom-label">{{$t('models.product.contact')}}</span>
                            <br>
                            <span>
                                {{model.contact}}
                                </span>
                        </el-col> -->
                    </el-row>
                     <el-row  :gutter="30" class="contact-info-card-row">
                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">{{$t('models.product.likes')}}</span>
                            <br>
                            <span class="custom-value">
                                {{model.likes_count}}
                            </span>
                        </el-col>

                        <el-col class="contact-info-card-col" :md="8">
                            <span class="custom-label">{{$t('models.product.comments')}}</span>
                            <br>
                            <span class="custom-value">
                                {{model.comments_count}}
                            </span>
                        </el-col>
                        <el-col class="contact-info-card-col" :md="8"></el-col>
                        <!--
                        <el-col :md="8">
                            <span class="custom-label">{{$t('models.product.comments')}}</span>
                            <br>
                            <span class="custom-value">
                                {{model.comments_count}}
                            </span>
                        </el-col>

                        <el-col :md="8">
                            <span class="custom-label">{{$t('models.product.published_at')}}</span>
                            <br>
                            <span class="custom-value" v-if="model.published_at">
                                    {{model.published_at}}
                                </span>
                            <span v-else>-</span>
                        </el-col>-->
                    </el-row> 
                </card>
                <card :loading="loading">
                    <div slot="header">
                        <p class="comments-header">{{$t('models.request.images')}}</p>
                    </div>
                    <upload-document @fileUploaded="uploadFiles" class="drag-custom" drag multiple/>
                    <div class="mt15">
                        <media :data="mediaFiles" @deleteMedia="deleteMedia"
                               v-if="media.length || (model.media && model.media.length)"></media>
                    </div>
                </card>
                <card class="mt15" v-if="model.id">
                    <div slot="header">
                        <p class="comments-header">{{$t('models.product.comments')}}</p>
                    </div>
                    <chat :id="model.id" type="product"/>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import {mapActions} from 'vuex';
    import ProductsMixin from 'mixins/adminProductsMixin';
    import Chat from 'components/Chat2';
    import EditActions from 'components/EditViewActions';
    import {Avatar} from 'vue-avatar'

    const mixin = ProductsMixin({mode: 'edit'});

    export default {
        mixins: [
            mixin
        ],
        components: {
            Chat,
            EditActions,
            Avatar
        },
        methods: {            
            ...mapActions([
                "deleteProduct"
            ]),
        }
    }
</script>

<style lang="scss" scoped>
    .custom-select {
        display: block;
    }

    .custom-label {
        color: #6AC06F;
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

    .custom-label {
        color: #6AC06F;
        display: inline-block;
        margin-bottom: 10px;
    }

    .custom-value {        
        line-height: 28px;
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
