<template>
    <div class="services">
        <heading :title="$t('models.service.title')" icon="ti-user" shadow="heavy">
            <template v-if="$can($permissions.create.provider)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">{{$t('actions.add')}}
                </el-button>
            </template>
            <template v-if="$can($permissions.delete.provider)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.service.delete')}}
                </el-button>
            </template>
        </heading>
        <list-table
            :fetchMore="fetchMore"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="items"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withSearch="false"
            @selectionChanged="selectionChanged"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';


    const mixin = ListTableMixin({
        actions: {
            get: 'getServices',
            delete: 'deleteService'
        },
        getters: {
            items: 'services',
            pagination: 'servicesMeta'
        }
    });

    export default {
        name: 'AdminServices',
        mixins: [mixin],
        components: {
            Heading
        },
        data() {
            return {
                header: [{
                    label: this.$t('models.service.name'),
                    withMultipleProps: true,
                    props: ['name', 'addr', 'cty']
                }, {
                    label: this.$t('models.service.contact_details'),
                    withMultipleProps: true,
                    props: ['email', 'phone']
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.service.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.provider
                        ]
                    }]
                }]
            }
        },
        computed: {
            filters() {
                return [
                    {
                        name: this.$t('filters.search'),
                        type: 'text',
                        icon: 'el-icon-search',
                        key: 'search'
                    }
                ]
            }
        },
        methods: {
            add() {
                this.$router.push({
                    name: 'adminServicesAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminServicesEdit',
                    params: {
                        id
                    }
                });
            }
        }
    }
</script>
