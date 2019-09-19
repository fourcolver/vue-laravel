<template>
    <div>
        <heading icon="ti-house" :title="$t('menu.addresses')" shadow="heavy" class="custom-heading">
            <el-button icon="ti-plus" type="primary" size="small" round @click="addAddress">
                {{$t('models.address.add')}}
            </el-button>
        </heading>
        <list-table
            :header="header"
            :items="getAddresses"
            :meta="{fetch: getMore}"
            :loading="{
                state: loading
            }"
            :pagination="{
            total,
            currPage,
            currSize
        }"/>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import ListTable from 'components/ListTable';
    import {displayError, displaySuccess} from "helpers/messages";
    import MixinBuilder from 'mixins/adminListMixinBuilder'

    const mixin = MixinBuilder.getListingMixin("address", "addresses");

    export default {
        name: 'AdminAddresses',
        mixins: [mixin],
        components: {
            Heading,
            ListTable
        },
        data() {
            return {
                header: [{
                    label: this.$t('models.address.city'),
                    prop: 'city'
                }, {
                    label: this.$t('models.address.country'),
                    prop: 'country_id'
                }, {
                    label: this.$t('models.address.state.label'),
                    prop: 'state_id'
                }, {
                    label: this.$t('models.address.street'),
                    prop: 'street'
                }, {
                    label: this.$t('models.address.street_nr'),
                    prop: 'street_nr'
                }, {
                    label: this.$t('models.address.zip'),
                    prop: 'zip'
                }, {
                    width: 80,
                    actions: [{
                        icon: 'ti-pencil',
                        title: this.$t('models.address.edit'),
                        onClick: this.edit
                    }, {
                        icon: 'ti-close',
                        title: this.$t('models.address.delete'),
                        onClick: this.remove,
                        style: {
                            color: 'red'
                        }
                    }]
                }]
            }
        }
    }
</script>

<style lang="scss" scoped>
    .custom-heading {
        margin-bottom: 2em;
    }
</style>