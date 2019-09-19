<template>
    <div class="templates">
        <!--        <heading style="background: transparent; padding: 0 20px;">-->
        <!--            <template v-if="$can($permissions.delete.template)">-->
        <!--                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"-->
        <!--                           type="danger">-->
        <!--                    {{$t('models.template.delete')}}-->
        <!--                </el-button>-->
        <!--            </template>-->
        <!--        </heading>-->
        <list-table
            :fetchMore="fetchMore"
            :fetchMoreParams="fetchParams"
            :filters="filters"
            :filtersHeader="filtersHeader"
            :header="header"
            :items="items"
            :loading="{state: loading}"
            :pagination="{total, currPage, currSize}"
            :withCheckSelection="false"
            :withSearch="false"
            @selectionChanged="selectionChanged"
            v-if="isReady"
        >
        </list-table>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTableMixin from 'mixins/ListTableMixin';


    const mixin = ListTableMixin({
        actions: {
            get: 'getTemplates',
            delete: 'deleteTemplate'
        },
        getters: {
            items: 'templates',
            pagination: 'templatesMeta'
        }
    });

    export default {
        components: {
            Heading
        },
        mixins: [mixin],
        data() {
            return {
                isReady: false,
                fetchParams: {},
                header: [{
                    label: this.$t('models.template.name'),
                    prop: 'name'
                }, {
                    label: this.$t('models.template.category'),
                    prop: 'category.name'
                }, {
                    width: 120,
                    actions: [{
                        icon: 'ti-pencil',
                        type: 'success',
                        title: this.$t('models.template.edit'),
                        onClick: this.edit,
                        permissions: [
                            this.$permissions.update.template
                        ]
                    }]
                }],
                building: {}
            };
        },
        methods: {
            add() {
                this.$router.push({
                    name: 'adminTemplatesAdd',
                    params: {
                        id: this.building.id
                    }
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminTemplatesEdit',
                    params: {
                        id
                    }
                });
            }
        },
        computed: {
            filters() {
                return [
                    // {
                    //     name: this.$t('filters.search'),
                    //     type: 'text',
                    //     icon: 'el-icon-search',
                    //     key: 'search'
                    // }
                ]
            }
        },
        created() {
            this.isReady = true;
        }
    }
</script>
