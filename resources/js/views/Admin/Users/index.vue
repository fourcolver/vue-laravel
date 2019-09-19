<template>
    <div class="services">
        <heading icon="ti-user" :title="$t('pages.user.title')" shadow="heavy">
            <template v-if="$can($permissions.create.user)">
                <el-button @click="add" icon="ti-plus" round size="small" type="primary">{{$t('actions.add')}}</el-button>
            </template>
            <template v-if="$can($permissions.delete.user)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.user.delete')}}
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
            get: 'getUsers',
            delete: 'deleteUser'
        },
        getters: {
            items: 'users',
            pagination: 'usersMeta'
        }
    });

    export default {
        name: 'AdminUsers',
        components: {
            Heading
        },
        mixins: [mixin],
        data() {
            return {
                header: [{
                    label: this.$t('models.user.name'),
                    prop: 'name'
                }, {
                    label: this.$t('models.user.email'),
                    prop: 'email'
                }, {
                    label: this.$t('models.user.phone'),
                    prop: 'phone'
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.user.edit_action'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.user
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
                    name: 'adminUsersAdd',
                    params: {
                        role: this.$route.query.role
                    }
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminUsersEdit',
                    params: {
                        id
                    }
                });
            }
        }
    }
</script>
