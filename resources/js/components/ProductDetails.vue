<template>
    <div class="product-details" v-if="!_.isEmpty(product)">
        <el-row :gutter="10">
            <el-col :md="6" :sm="12">
                <p class="product-label">{{$t('models.user.name')}}</p>
                <p v-if="product.tenant">
                    <router-link :to="{name: 'adminTenantsEdit', params: {id: product.tenant.id}}">
                        {{product.tenant.name}}
                    </router-link>
                </p>
                <p v-else>
                    <router-link :to="{name: 'adminUsersEdit', params: {id: product.user_id}}">
                        {{product.user ? product.user.name:""}}
                    </router-link>
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="product-label">{{$t('models.product.type.label')}}</p>
                <p>
                    {{$t(`models.product.type.${productConstants.type[product.type]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="product-label">{{$t('models.product.visibility.label')}}</p>
                <p>
                    {{$t(`models.product.visibility.${productConstants.visibility[product.visibility]}`)}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="product-label">{{$t('models.product.status.label')}}</p>
                <p>
                    {{$t(`models.product.status.${productConstants.status[product.status]}`)}}
                </p>
            </el-col>
            <el-col :md="2" :sm="12">
                <p class="product-label">{{$t('models.product.likes')}}</p>
                <p>
                    {{product.likes_counter}}
                </p>
            </el-col>
            <el-col :md="4" :sm="12">
                <p class="product-label">{{$t('models.product.published_at')}}</p>
                <p>
                    {{product.published_at}}
                </p>
            </el-col>
        </el-row>
        <el-row v-if="product.title">
            <el-col :span="24">
                <p class="product-label">{{$t('models.product.product_title')}}</p>
                <p>
                    {{product.title}}
                </p>
            </el-col>
        </el-row>
        <el-row>
            <el-col :span="24">
                <p class="product-label">{{$t('models.product.content')}}</p>
                <p>
                    {{product.content}}
                </p>
            </el-col>
        </el-row>
        <gallery :data="product.media" :withDelete="false" v-if="product.media && product.media.length"></gallery>
    </div>
</template>

<script>
    import Gallery from 'components/RequestMedia';

    export default {
        name: "ProductDetails",
        components: {
            Gallery
        },
        props: {
            product: {
                type: Object,
                required: true
            }
        },
        data() {
            return {
                productConstants: this.$store.getters['application/constants'].products
            }
        }
    }
</script>

<style scoped>
    .product-label {
        color: #409EFF;
    }
</style>
