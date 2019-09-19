<template>
    <div class="products">
        <heading :title="$t('models.product.title')" icon="ti-announcement" shadow="heavy">
            <!--<el-button @click="add" icon="ti-plus" round type="primary">-->
            <!--{{$t('models.product.add')}}-->
            <!--</el-button>-->
            <template v-if="$can($permissions.delete.product)">
                <el-button :disabled="!selectedItems.length" @click="batchDelete" icon="ti-trash" round size="small"
                           type="danger">
                    {{$t('models.product.delete_action')}}
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
        <el-dialog :title="$t('models.product.details')" :visible.sync="productDetailsVisible">
            <product-details :product="product"></product-details>
            <el-button @click="changeProductStatus(product.id, productConstants.published)"
                       type="success"
                       v-if="product.status != productConstants.published"
            >
                {{$t('models.product.publish')}}
            </el-button>
            <el-button @click="changeProductStatus(product.id, productConstants.unpublished)"
                       type="danger"
                       v-else
            >
                {{$t('models.product.unpublish')}}
            </el-button>
        </el-dialog>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {mapActions} from 'vuex';
    import {displayError, displaySuccess} from "helpers/messages";
    import ListTableMixin from 'mixins/ListTableMixin';
    import ProductDetails from "components/ProductDetails";
    import getFilterDistricts from 'mixins/methods/getFilterDistricts';
    
    const mixin = ListTableMixin({
        actions: {
            get: 'getProducts',
            delete: 'deleteProduct'
        },
        getters: {
            items: 'products',
            pagination: 'productsMeta'
        }
    });

    export default {
        components: {
            Heading,
            ProductDetails
        },
        mixins: [mixin, getFilterDistricts],
        data() {
            return {
                header: [{
                    label: this.$t('models.product.product_title'),
                    prop: 'title'
                }, {
                    label: this.$t('models.user.email'),
                    prop: 'user.email'
                }, {
                    label: this.$t('models.product.type.label'),
                    prop: 'type_label'
                }, {
                    label: this.$t('models.product.visibility.label'),
                    prop: 'visibility_label'
                }, {
                    label: this.$t('models.product.status.label'),
                    prop: 'status_label'
                }, {
                    // width: 170,
                    width: 85,
                    actions: [
                        // {
                        //     type: 'primary',
                        //     title: this.$t('models.product.show'),
                        //     onClick: this.show,
                        //     permissions: [
                        //         this.$permissions.view.product
                        //     ]
                        // }, 
                        {
                            type: 'success',
                            title: this.$t('models.product.edit'),
                            onClick: this.edit,
                            permissions: [
                                this.$permissions.update.product
                            ]
                        }
                    ]
                }],
                product: {},
                productDetailsVisible: false,
            };
        },
        computed: {
            formattedItems() {
                return this.items.map((product) => {
                    product.status_label = this.$t(`models.product.status.${product.status_label}`);
                    product.visibility_label = this.$t(`models.product.visibility.${product.visibility_label}`);
                    product.type_label = this.$t(`models.product.type.${product.type_label}`);
                    return product
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
                        name: this.$t('models.product.status.label'),
                        type: 'select',
                        key: 'status',
                        data: this.prepareFilters("status"),
                    },
                    {
                        name: this.$t('models.product.type.label'),
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
                ]
            },
            productConstants() {
                return this.$store.getters['application/constants'].products;
            },

        },
        methods: {
            ...mapActions(['changeProductPublish', 'getBuildings']),
            async getFilterBuildings() {
                this.loading = true;
                const buildings = await this.getBuildings({
                    get_all: true
                });
                this.loading = false;

                return buildings.data;
            },
            add() {
                this.$router.push({
                    name: 'adminProductsAdd'
                });
            },
            edit({id}) {
                this.$router.push({
                    name: 'adminProductsEdit',
                    params: {
                        id
                    }
                });
            },
            show(product) {
                this.product = product;
                this.productDetailsVisible = true;
            },
            changeProductStatus(id, status) {
                this.changeProductPublish({id, status}).then((resp) => {
                    this.getProducts();
                    this.productDetailsVisible = false;
                    displaySuccess(resp);
                }).catch((error) => {
                    displayError(error);
                });
            },
            prepareFilters(property) {
                return Object.keys(this.productConstants[property]).map((id) => {
                    return {
                        id: parseInt(id),
                        name: this.$t(`models.product.${property}.${this.productConstants[property][id].toLowerCase()}`)
                    };
                });
            },
        }
    }
</script>
