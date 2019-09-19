<template>
    <placeholder :size="256" :src="require('img/5cf66b5b3c55f.png')" v-if="!loading.visible && !contract">
        There is no contract available.
        <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
    </placeholder>
    <div class="contracts" v-else-if="contract">
        <el-card v-sticky="{stickyTop: -16}">
            <heading icon="ti-book" title="My contract">
                <div slot="description">Curabitur pellentesque consectetur malesuada.</div>
            </heading>
        </el-card>
        <el-divider />
        <el-card>
            <el-divider class="column-divider" content-position="left">Building</el-divider>
            <div>
                <b>Name: </b>
                <div>{{contract.address.street}} {{contract.address.street_nr}}</div>
                <div>{{contract.address.zip}} {{contract.address.city}}</div>
            </div>
            <el-divider class="column-divider" content-position="left">Unit</el-divider>
            <div>
                <b>Type:</b>
                {{getUnitType(contract.unit.type)}}
            </div>
            <div>
                <b>Unit number:</b>
                {{contract.unit.room_no}}
            </div>
            <div>
                <b>Floor:</b>
                {{contract.unit.floor}}
            </div>
            <div v-if="contract.unit.basement">
                <b>Basement:</b>
                Yes
            </div>
            <div v-if="contract.unit.attic">
                <b>Attic:</b>
                Yes
            </div>
            <div>
                <b>Monthly rent:</b>
                {{contract.unit.monthly_rent}}
            </div>
            <template v-if="contract.rent_start">
                <el-divider content-position="left">Rent date</el-divider>
                <el-tag class="rent" type="warning" disable-transitions>
                    Start date:  
                    <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_start | formatDate}}</el-tag>
                    <template v-if="contract.rent_end">
                        End date: <el-tag type="warning" effect="plain" disable-transitions>{{contract.rent_end | formatDate}}</el-tag>
                    </template>
                </el-tag>
            </template>
            <template v-if="contract.file">
                <el-divider content-position="left">Rent contract file</el-divider>
                <el-image :src="contract.file.url" v-if="isFileImage(contract.file)" />
                <embed :src="contract.file.url" v-else />
            </template>
        </el-card>
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    import Placeholder from 'components/Placeholder'
    import {displayError} from 'helpers/messages'
    import unitTypes from 'mixins/methods/unitTypes'
    import {format} from 'date-fns'
    import VueSticky from 'vue-sticky'

    export default {
        mixins: [unitTypes],
        components: {
            Heading,
            Placeholder
        },
        directives: {
            sticky: VueSticky
        },
        filters: {
            formatDate (date) {
                return format(date, 'DD.MM.YYYY')
            }
        },
        data () {
            return {
                contract: null,
                loading: {
                    visible: true
                }
            }
        },
        methods: {
            isFileImage (file) {
                const ext = file.name.split('.').pop()

                return ['jpg', 'jpeg', 'gif', 'bmp', 'png'].includes(ext);
            },
            getUnitType(type) {
                const {label} = this.unitTypes.find(unit => unit.type === type);

                return label
            }
        },
        async mounted () {
            this.loading = this.$loading({
                target: this.$el.parentElement,
                text: 'Fetching your contract...'
            })

            try {
                const {data: {unit, media, address, contract, rent_start, rent_end}} = await this.$store.dispatch('myTenancy')

                this.contract = {unit, address, rent_start, rent_end}

                if (media.length) {
                    this.contract.file = media[media.length - 1]
                }
            } catch (error) {
                displayError(error)
            } finally {
                this.loading.close()
            }
        }
    }
</script>

<style lang="scss" scoped>
   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: darken(#F2F4F9, 32%);

        small {
            font-size: 72%;
            color: darken(#F2F4F9, 16%);
        }
    }
    .contracts {

        &:not(.empty):before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5d066fc2eaf44.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }

        .el-card {
            position: relative;
            z-index: 1;
            max-width: 1024px;

            &:first-child {
                :global(.el-card__body) {
                    padding: 12px 16px;
                    .heading div {
                        color: darken(#fff, 40%);
                    }
                }
            }

            &:last-child :global(.el-card__body) {
                padding: 16px;

                > div {
                    &.el-divider {
                        :global(.el-divider__text.is-left) {
                            font-size: 16px;
                            left: 0;
                            padding-left: 0;
                        }
                    }

                    &:not(.el-divider):not(.el-image) {
                        &:not(:last-child) {
                            margin-bottom: 8px;
                        }
                    }
                }

                > .el-tag {
                    width: 100%;
                    height: auto;
                    padding: 4px 8px;
                    font-size: 14px;

                    .el-tag {
                        height: auto;
                        font-size: 14px;
                        font-weight: bold;
                        line-height: 1.8;
                        border-radius: 12px;
                    }
                }
                
                embed {
                    width: 100%;
                    height: 100vh;
                }
            }
        }
    }
</style>