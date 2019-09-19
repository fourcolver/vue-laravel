<template>
    <div class="services">
        <heading :title="$t('models.request.title')" icon="ti-user" shadow="heavy">
            <template v-if="$can($permissions.create.request)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">
                    {{$t('models.request.add_title')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.request)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.request.delete')}}
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
    </div>
</template>

<script>
    import {mapActions, mapState} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';
    import getFilterPropertyManager from 'mixins/methods/getFilterPropertyManager';
    import PrepareCategories from 'mixins/methods/prepareCategories';
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';


    const mixin = ListTableMixin({
        actions: {
            get: 'getRequests',
            delete: 'deleteRequest'
        },
        getters: {
            items: 'requests',
            pagination: 'requestsMeta'
        }
    });

    export default {
        name: 'AdminRequests',
        mixins: [mixin, getFilterPropertyManager, PrepareCategories, getFilterDistricts],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: this.$t('models.request.category'),
                    withMultipleProps: true,
                    props: ['parent_category_name', 'category_name']
                }, {
                    label: this.$t('models.request.address'),
                    withMultipleProps: true,
                    props: ['address', 'zip']
                }, {
                    label: this.$t('models.request.created_by'),
                    withMultipleProps: true,
                    props: ['tenant_name', 'created_at']
                }, {
                    width: 110,
                    label: this.$t('models.request.assigned_to'),
                    withUsers: true,
                    prop: 'assignedUsers',
                    count: 'assignedUsersCount'
                }, {
                    width: 100,
                    label: this.$t('models.request.priority.label'),
                    withBadgeProps: true,
                    prop: 'priority_label',
                    size: 'small'
                }, {
                    label: this.$t('models.request.status.label'),
                    prop: 'status',
                    i18nPath: 'models.request.status',
                    class: 'rounded-select',
                    select: {
                        icon: 'ti-pencil',
                        data: [],
                        getter: "application/requests",
                        withDisabled: "statusByAgent",
                        onChange: this.listingSelectChangedNotify
                    }
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.request.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.request,
                            this.$permissions.update.serviceRequest
                        ]
                    }]
                }]
            }
        },
        computed: {
            ...mapState("application", {
                requestConstants(state) {
                    return state.constants.service_requests;
                }
            }),
            formattedItems() {
                return this.items.map((request) => {
                    request.qualification_label = this.$t(`models.request.qualification.${request.qualification_label}`);
                    return request
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
                        name: this.$t('filters.categories'),
                        type: 'select',
                        key: 'category_id',
                        data: [],
                        fetch: this.getFilterCategories
                    },
                    {
                        name: this.$t('models.request.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.request.priority.label'),
                        type: 'select',
                        key: 'priority',
                        data: this.prepareFilters("priority"),
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
                        name: this.$t('filters.propertyManagers'),
                        type: 'select',
                        key: 'assignee_id',
                        data: [],
                        fetch: this.getFilterPropertyManagers
                    },
                    {
                        name: this.$t('filters.services'),
                        type: 'select',
                        key: 'service_id',
                        data: [],
                        fetch: this.getFilterServices
                    },
                    {
                        name: this.$t('filters.tenant'),
                        type: 'remote-select',
                        key: 'tenant_id',
                        data: [],
                        remoteLoading: false,
                        fetch: this.fetchRemoteTenants
                    },
                    {
                        name: this.$t('filters.created_from'),
                        type: 'date',
                        key: 'created_from',
                        format: 'dd.MM.yyyy'
                    },
                    {
                        name: this.$t('filters.created_to'),
                        type: 'date',
                        key: 'created_to',
                        format: 'dd.MM.yyyy'
                    },
                    {
                        name: this.$t('models.request.closed_date'),
                        type: 'date',
                        key: 'solved_date',
                        format: 'dd.MM.yyyy'
                    }
                ]
            }
        },
        methods: {
            ...mapActions(['updateRequest', 'getRequestCategoriesTree', 'getServices', 'getBuildings', 'getTenants']),
            async getFilterBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            async getFilterCategories() {
                this.loading = true;
                const categoriesResp = await this.getRequestCategoriesTree({});
                const categories = this.prepareCategories(categoriesResp.data);
                this.loading = false;

                return categories;
            },
            async getFilterServices() {
                this.loading = true;
                const services = await this.getServices({get_all: true});
                this.loading = false;

                return services.data;
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
            add() {
                this.$router.push({
                    name: 'adminRequestsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminRequestsEdit',
                    params: {
                        id
                    }
                });
            },
            prepareFilters(property) {
                return Object.keys(this.requestConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.request.${property}.${this.requestConstants[property][id]}`)
                    };
                });
            },
            listingSelectChangedNotify(row) {
                this.$confirm(this.$t(`models.request.confirmChange.title`), this.$t('models.request.confirmChange.warning'), {
                    confirmButtonText: this.$t(`models.request.confirmChange.confirmBtnText`),
                    cancelButtonText: this.$t(`models.request.confirmChange.cancelBtnText`),
                    type: 'warning'
                }).then(async () => {
                    try {
                        this.loading = true;
                        const resp = await this.updateRequest(row);
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
            async listingSelectChanged(row) {
                try {
                    this.loading = true;
                    const resp = await this.updateRequest(row);
                    await this.fetchMore();
                    displaySuccess(resp);
                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading = false;
                }
            },
            managersMapper(propertyManagers) {
                return propertyManagers.map((propertyManager) => {
                    return {
                        id: propertyManager.user.id,
                        name: propertyManager.user.name
                    }
                })
            }
        }
    }
</script>
