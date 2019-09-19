<template>
    <div class="tenants-edit mb20">
        <heading :title="$t('models.tenant.edit_title')" icon="ti-home" shadow="heavy">
            <edit-actions :saveAction="submit" :deleteAction="deleteTenant" route="adminTenants"/>
        </heading>
        <el-row :gutter="20" class="crud-view">
            <el-col :md="12">
                <el-tabs type="border-card">
                    <el-tab-pane :label="$t('models.tenant.details')" v-loading="loading.state">
                        <el-form :model="model" label-position="top" label-width="192px" ref="form">
                            <el-form-item>
                                <el-button @click="downloadCredentials" size="mini"
                                           type="primary">
                                    {{$t('models.tenant.download_credentials')}}
                                </el-button>
                                <el-button @click="sendCredentials" size="mini"
                                           type="primary">
                                    {{$t('models.tenant.send_credentials')}}
                                </el-button>
                            </el-form-item>
                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.personal_details_card')}}
                            </el-divider>
                            <el-row :gutter="20">
                                <el-col :md="12">
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
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.company')" :rules="validationRules.company"
                                                  prop="company"
                                                  v-if="model.title === titles.company">
                                        <el-input autocomplete="off" type="text" v-model="model.company"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.first_name')"
                                                  :rules="validationRules.first_name"
                                                  prop="first_name">
                                        <el-input autocomplete="off" type="text" v-model="model.first_name"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.last_name')"
                                                  :rules="validationRules.last_name"
                                                  prop="last_name">
                                        <el-input autocomplete="off" type="text" v-model="model.last_name"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.birth_date')"
                                                  :rules="validationRules.birth_date"
                                                  prop="birth_date">
                                        <el-date-picker
                                            :placeholder="$t('models.tenant.birth_date')"
                                            format="dd.MM.yyyy"
                                            style="width: 100%;"
                                            type="date"
                                            v-model="model.birth_date"
                                            value-format="yyyy-MM-dd"/>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">

                                </el-col>
                            </el-row>


                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.contact_info_card')}}
                            </el-divider>

                            <el-row :gutter="20">
                                <el-col :md="12">

                                    <el-form-item :label="$t('models.tenant.mobile_phone')" prop="mobile_phone">
                                        <el-input autocomplete="off" type="text"
                                                  v-model="model.mobile_phone"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.private_phone')" prop="private_phone">
                                        <el-input autocomplete="off" type="text"
                                                  v-model="model.private_phone"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>


                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.work_phone')" prop="work_phone">
                                        <el-input autocomplete="off" type="text" v-model="model.work_phone"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">

                                </el-col>
                            </el-row>

                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.account_info_card')}}
                            </el-divider>

                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.user.profile_image')">
                                        <cropper :resize="false" :viewportType="'circle'" @cropped="cropped"/>
                                        <img :src="`/${model.avatar}?${Date.now()}`"
                                             style="width: 100%" v-if="!avatar.length && model.avatar">
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('email')" :rules="validationRules.email" prop="email">
                                        <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>


                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('password')" :rules="validationRules.password"
                                                  prop="password">
                                        <el-input autocomplete="off" type="password"
                                                  v-model="model.password"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">

                                    <el-form-item :label="$t('confirm_password')"
                                                  :rules="validationRules.password_confirmation"
                                                  prop="password_confirmation">
                                        <el-input autocomplete="off" type="password"
                                                  v-model="model.password_confirmation"></el-input>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.status.label')"
                                                  :rules="validationRules.status"
                                                  prop="status">
                                        <el-select style="display: block" v-model="model.status">
                                            <el-option
                                                :key="k"
                                                :label="$t(`models.tenant.status.${status}`)"
                                                :value="parseInt(k)"
                                                v-for="(status, k) in constants.tenants.status">
                                            </el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">

                                </el-col>
                            </el-row>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.tenant.contract')">
                        <el-form :model="model" label-position="top" label-width="192px" ref="form">

                            <el-divider class="column-divider" content-position="left">
                                {{$t('models.tenant.rent_contract')}}
                            </el-divider>
                            <el-row :gutter="20">
                                <el-col :md="12">
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
                                </el-col>

                                <el-col :md="12">
                                    <el-form-item :label="$t('models.tenant.rent_end')"
                                                  prop="rent_end">
                                        <el-date-picker
                                            :picker-options="{disabledDate: disabledRentEnd}"
                                            :placeholder="$t('models.tenant.rent_end')"
                                            format="dd.MM.yyyy"
                                            style="width: 100%;"
                                            type="date"
                                            v-model="model.rent_end"
                                            value-format="yyyy-MM-dd"/>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                            <el-form-item>
                                <el-row :gutter="20">
                                    <el-col :md="12">
                                        <upload-document @fileUploaded="contractUploaded" class="drag-custom" acceptType=".pdf" drag/>
                                    </el-col>
                                    <el-col :md="12">
                                        <el-row :gutter="20" class="list-complete-item" justify="center"
                                                style="margin-bottom: 1em;"
                                                type="flex"
                                                v-if="lastMedia && lastMedia.name">
                                            <el-col :span="20">
                                                <a :href="lastMedia.url" target="_blank"><strong>{{ lastMedia.name
                                                    }}</strong></a>
                                            </el-col>
                                            <el-col :span="4">
                                                <el-button @click="deleteMedia" icon="ti-trash" size="mini"
                                                           type="danger"/>
                                            </el-col>
                                        </el-row>
                                        <template v-if="lastMedia && lastMedia.name">
                                            <embed :src="lastMedia.url" style="width: 100%" v-if="isFilePDF(lastMedia)"/>
                                        </template>
                                    </el-col>
                                </el-row>
                            </el-form-item>
                        </el-form>
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.tenant.building.name')">
                        <el-form :model="model" label-width="80px" ref="form" style="max-width: 512px;">
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
                        </el-form>
                    </el-tab-pane>

                </el-tabs>
            </el-col>
            <el-col :md="12">
                <el-tabs type="border-card">
                    <el-tab-pane :label="$t('models.tenant.requests')">
                        <relation-list
                            :actions="requestActions"
                            :columns="requestColumns"
                            :filterValue="model.id"
                            fetchAction="getRequests"
                            filter="tenant_id"
                            v-if="model.id"
                        />
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.tenant.posts')">
                        <relation-list
                            :actions="postActions"
                            :columns="postColumns"
                            :filterValue="user.id"
                            fetchAction="getPostsTruncated"
                            filter="user_id"
                            v-if="!_.isEmpty(user)"
                        />
                    </el-tab-pane>
                    <el-tab-pane :label="$t('models.tenant.products')">
                        <relation-list
                            :actions="productActions"
                            :columns="productColumns"
                            :filterValue="user.id"
                            fetchAction="getProducts"
                            filter="user_id"
                            v-if="!_.isEmpty(user)"
                        />
                    </el-tab-pane>
                </el-tabs>
                <!--                <raw-grid-statistics-card :data="statistics"/>-->
                <!--                <colored-statistics-card-->
                <!--                    color="#0F52BC"-->
                <!--                    description="65.45% on average time"-->
                <!--                    icon="ti-timer"-->
                <!--                    title="Bounce rate"-->
                <!--                    value="32.16%"/>-->
                <!--                <colored-statistics-card-->
                <!--                    color="#27ae60"-->
                <!--                    description="65.45% on average time"-->
                <!--                    icon="ti-timer"-->
                <!--                    title="Bounce rate"-->
                <!--                    value="32.16%"/>-->
                <!--                <progress-statistics-card-->
                <!--                    color="#f39c12"-->
                <!--                    description="3 more than in August"-->
                <!--                    title="Activities completed"-->
                <!--                    value="64"/>-->
                <!--                <el-row :gutter="15" type="flex">-->
                <!--                    <el-col :span="12">-->
                <!--                        <circular-progress-statistics-card :percentage="56" title="Lorep ipsum"/>-->
                <!--                    </el-col>-->
                <!--                    <el-col :span="12">-->
                <!--                        <circular-progress-statistics-card-->
                <!--                            :percentage="88"-->
                <!--                            color="#94579F"-->
                <!--                            title="Lorep ipsum"/>-->
                <!--                    </el-col>-->
                <!--                </el-row>-->
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import RawGridStatisticsCard from 'components/RawGridStatisticsCard';
    import CircularProgressStatisticsCard from 'components/CircularProgressStatisticsCard';
    import ColoredStatisticsCard from 'components/ColoredStatisticsCard.vue';
    import ProgressStatisticsCard from 'components/ProgressStatisticsCard.vue';
    import AdminTenantsMixin from 'mixins/adminTenantsMixin';
    import UploadDocument from 'components/UploadDocument';
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import Cropper from 'components/Cropper';
    import RelationList from 'components/RelationListing';
    import EditActions from 'components/EditViewActions';
    import globalFunction from "helpers/globalFunction";

    const mixin = AdminTenantsMixin({
        mode: 'edit'
    });

    export default {
        mixins: [mixin, globalFunction],
        components: {
            Heading,
            Card,
            RawGridStatisticsCard,
            CircularProgressStatisticsCard,
            ColoredStatisticsCard,
            ProgressStatisticsCard,
            UploadDocument,
            Cropper,
            RelationList,
            EditActions
        },
        data() {
            return {
                statistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#f06292',
                    value: 648,
                    description: 'Items sold'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#26c6da',
                    value: '47.5k',
                    description: 'Followers'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Daily earnings'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Products'
                }],
                liteStatistics: [{
                    icon: 'ti-shopping-cart',
                    color: '#9575cd',
                    value: 764,
                    description: 'Daily earnings'
                }, {
                    icon: 'ti-shopping-cart',
                    color: '#1a237e',
                    value: 256,
                    description: 'Products'
                }],
                requestColumns: [{
                    prop: 'category.name',
                    label: this.$t('models.request.category')
                }, {
                    prop: 'status',
                    i18n: this.requestStatusLabel,
                    withBadge: this.requestStatusBadge,
                    label: this.$t('models.request.status.label')
                }],
                requestActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.request.edit'),
                        type: 'primary',
                        onClick: this.requestEditView
                    }]
                }],
                postColumns: [{
                    prop: 'preview',
                    label: this.$t('models.post.preview')
                }],
                postActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.post.edit'),
                        type: 'primary',
                        onClick: this.postEditView
                    }]
                }],
                productColumns: [{
                    prop: 'title',
                    label: this.$t('models.product.title')
                }],
                productActions: [{
                    width: '180px',
                    buttons: [{
                        title: this.$t('models.product.edit'),
                        type: 'primary',
                        onClick: this.productEditView
                    }]
                }],
            }
        },
        methods: {
            ...mapActions(['deleteMediaFile', 'downloadTenantCredentials', 'sendTenantCredentials', 'deleteTenant']),
            deleteMedia() {
                this.deleteMediaFile({
                    id: this.model.id,
                    media_id: this.lastMedia.id
                }).then(r => {
                    displaySuccess(r);

                    this.model.media.splice(-1, 1);
                }).catch(err => {
                    displayError(err);
                });
            },
            requestEditView(request) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id: request.id
                    }
                })
            },
            postEditView(post) {
                this.$router.push({
                    name: 'adminPostsEdit',
                    params: {
                        id: post.id
                    }
                })
            },
            productEditView(product) {
                this.$router.push({
                    name: 'adminProductsEdit',
                    params: {
                        id: product.id
                    }
                })
            },
            async downloadCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.downloadTenantCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        const url = window.URL.createObjectURL(new Blob([resp.data], {type: resp.headers['content-type']}));
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', resp.headers['content-disposition'].split('filename=')[1]);
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        window.URL.revokeObjectURL(url);
                    }
                } catch (e) {
                    displayError({
                        success: false,
                        message: this.$t('models.tenant.credentials_download_failed')
                    })
                } finally {
                    this.loading.state = false;
                }
            },
            async sendCredentials() {
                this.loading.state = true;
                try {
                    const resp = await this.sendTenantCredentials({id: this.model.id});
                    if (resp && resp.data) {
                        displaySuccess({
                            success: true,
                            message: this.$t('models.tenant.credentials_sent')
                        });
                    }
                } catch (e) {
                    displayError({
                        success: true,
                        message: this.$t('models.tenant.credentials_send_fail')
                    });
                } finally {
                    this.loading.state = false;
                }
            },           
            requestStatusBadge(status) {                
                return this.getRequestStatusColor(status);
            },
            requestStatusLabel(status) {
                return this.$t(`models.request.status.${this.requestStatusConstants[status]}`)
            }
        },
        computed: {
            ...mapGetters('application', {
                constants: 'constants'
            }),
            lastMedia() {
                return this.model.media[this.model.media.length - 1];
            },
            requestStatusConstants() {
                return this.constants.serviceRequests.status
            }
        }
    }
</script>

<style lang="scss" scoped>
    .tenants-edit {
        .heading {
            margin-bottom: 20px;
        }

        > .el-row > .el-col {
            &:first-of-type .el-card:not(:last-of-type) {
                margin-bottom: 2em;
            }

            &:last-of-type {
                /*min-width: 448px;*/
                /*max-width: 576px;*/

                > *:not(:last-of-type) {
                    margin-bottom: 1em;
                }
            }
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
