<template>
    <div class="posts">
        <heading :title="$t('models.post.title')" icon="ti-announcement" shadow="heavy">
            <template v-if="$can($permissions.create.post)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">
                    {{$t('models.post.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.post)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.post.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="formattedItems"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
        <el-dialog :title="$t('models.post.details')" :visible.sync="postDetailsVisible">
            <post-details :post="post" style="margin-bottom: 15px;"></post-details>
            <el-button @click="changePostStatus(post.id, postConstants.published)"
                       type="success"
                       v-if="post.status != postConstants.published"
            >
                {{$t('models.post.publish')}}
            </el-button>
            <el-button @click="changePostStatus(post.id, postConstants.unpublished)"
                       type="danger"
                       v-else
            >
                {{$t('models.post.unpublish')}}
            </el-button>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import ListTableMixin from 'mixins/ListTableMixin';
    import PostDetails from "components/PostDetails";
    import postConstants from 'mixins/methods/postTypes';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';


    const mixin = ListTableMixin({
        actions: {
            get: 'getPosts',
            delete: 'deletePost'
        },
        getters: {
            items: 'posts',
            pagination: 'postsMeta'
        }
    });

    export default {
        components: {
            Heading,
            PostDetails
        },
        mixins: [mixin, getFilterDistricts],
        data() {
            return {
                header: [{
                    label: this.$t('models.post.preview'),
                    prop: 'preview'
                }, {
                    label: this.$t('models.user.email'),
                    prop: 'user.email'
                }, {
                    label: this.$t('models.post.type.label'),
                    prop: 'type_label'
                }, {
                    label: this.$t('models.post.visibility.label'),
                    prop: 'visibility_label'
                }, {
                    label: this.$t('models.post.status.label'),
                    prop: 'status',
                    i18nPath: 'models.post.status',
                    class: 'rounded-select',
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "application/posts",
                        onChange: this.listingSelectChangedNotify
                    }
                }, {
                    // width: 170,
                    width: 85,
                    actions: [
                        // {
                        //     type: 'primary',
                        //     title: this.$t('models.post.show'),
                        //     onClick: this.show,
                        //     permissions: [
                        //         this.$permissions.view.post
                        //     ],
                        //     hidden: this.checkPostType
                        // }, 
                        {
                            type: 'success',
                            title: this.$t('models.post.edit'),
                            onClick: this.edit,
                            permissions: [
                                this.$permissions.update.post
                            ],
                            hidden: this.checkPostType
                        }
                    ]
                }],
                post: {},
                postDetailsVisible: false,
                postConstants
            };
        },
        computed: {
            ...mapState("application", {
                postsConstants(state) {
                    return state.constants.posts;
                }
            }),
            formattedItems() {
                return this.items.map((post) => {
                    post.status_label = this.$t(`models.post.status.${post.status_label}`);
                    post.visibility_label = this.$t(`models.post.visibility.${post.visibility_label}`);
                    post.type_label = this.$t(`models.post.type.${post.type_label}`);
                    return post;
                });
            },
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    },
                    {
                        name: this.$t('models.post.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.post.type.label'),
                        type: 'select',
                        key: 'type',
                        data: this.prepareFilters("type"),
                    },
                    {
                        name: this.$t('filters.districts'),
                        type: 'select',
                        key: 'district_id',
                        data: [],
                        fetch: this.getFilterDistricts
                    },
                    {
                        name: this.$t('filters.buildings'),
                        type: 'select',
                        key: 'building_id',
                        data: [],
                        fetch: this.getFilterBuildings
                    },
                    {
                        name: this.$t('filters.tenant'),
                        type: 'remote-select',
                        key: 'tenant_id',
                        data: [],
                        remoteLoading: false,
                        fetch: this.fetchRemoteTenants
                    },
                ]
            }
        },
        methods: {
            ...mapActions(['changePostPublish', 'updatePost', 'getBuildings', 'getTenants']),
            async getFilterBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            checkPostType(post) {
                return post.type === 2;
            },
            add() {
                this.$router.push({
                    name: 'adminPostsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminPostsEdit',
                    params: {
                        id
                    }
                });
            },
            show(post) {
                this.post = post;
                this.postDetailsVisible = true;
            },
            changePostStatus(id, status) {
                this.changePostPublish({id, status}).then((resp) => {
                    this.fetchMore();
                    this.postDetailsVisible = false;
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`models.post.confirmChange.title`), this.$t('models.post.confirmChange.warning'), {
                    confirmButtonText: this.$t(`models.post.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`models.post.confirmChange.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;

                        const resp = await this.updatePost(row);
                        await this.fetchMore();
                        displaySuccess(resp);
                    } catch (err) {
                        displayError(err);
                    } finally {
                        this.loading = false;
                    }
                }).catch(async () => {
                    this.loading = true;
                    await this.fetchMore();
                    this.loading = false;
                });
            },
            prepareFilters(property) {
                return Object.keys(this.postsConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.post.${property}.${this.postsConstants[property][id]}`)
                    };
                });
            },
            async fetchRemoteTenants(search) {
                const tenants = await this.getTenants({get_all: true, search});

                return tenants.data.map((tenant) => {
                    return {
                        name: `${tenant.first_name} ${tenant.last_name}`,
                        id: tenant.id
                    };
                });
            },
        }
    }
</script>
