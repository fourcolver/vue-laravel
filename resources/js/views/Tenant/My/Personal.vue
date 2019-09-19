<template>
    <div :class="['personal', {empty: !model}, {sm: el.is.sm}]">
        <placeholder :size="256" :src="require('img/5d0672abb48ed.png')" v-if="!model && !loading.visible">
            No personal data available.
            <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
        </placeholder>
        <template v-else-if="model">
            <heading icon="ti-book" title="Personal data">
                <div slot="description" class="description">My personal details.</div>
            </heading>
            <el-card ref="card" v-loading="loading.visible">
                <el-form :label-position="labelPosition" :model="model" label-width="144px" ref="form">
                    <el-form-item label="Title" prop="title">
                        <el-select placeholder="Select title" v-model="model.title">
                                <el-option v-for="title in $constants.tenants.title" :key="title" :label="$t(`models.tenant.titles.${title}`)" :value="title" />
                        </el-select>
                    </el-form-item>
                    <el-form-item label="Company name" prop="company" v-if="model.title === 'company'">
                        <el-input type="text" v-model="model.company" />
                    </el-form-item>
                    <el-form-item :rules="validationRules.first_name" label="First name" prop="first_name">
                        <el-input type="text" v-model="model.first_name"/>
                    </el-form-item>
                    <el-form-item :rules="validationRules.last_name" label="Last name" prop="last_name">
                        <el-input type="text" v-model="model.last_name"/>
                    </el-form-item>
                    <el-form-item :rules="validationRules.birth_date" label="Birth date" prop="birth_date">
                        <el-date-picker format="dd.MM.yyyy" type="date" v-model="model.birth_date" value-format="yyyy-MM-dd" />
                    </el-form-item>
                    <el-form-item label="Mobile phone" prop="mobile_phone">
                        <el-input type="text" v-model="model.mobile_phone"/>
                    </el-form-item>
                    <el-form-item label="Work phone" prop="work_phone">
                        <el-input type="text" v-model="model.work_phone"/>
                    </el-form-item>
                    <el-form-item label="Personal phone" prop="private_phone">
                        <el-input type="text" v-model="model.private_phone"/>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" icon="ti-save" :disabled="loading.visible" @click="submit">
                            Save
                        </el-button>
                    </el-form-item>
                </el-form>
            </el-card>
        </template>
        
    </div>
</template>

<script>
    import Heading from 'components/Heading'
    import Placeholder from 'components/Placeholder'
    import unitTypes from 'mixins/methods/unitTypes'
    import {displayError, displaySuccess} from 'helpers/messages'
    import VueSticky from 'vue-sticky'
    import {ResponsiveMixin} from 'vue-responsive-components'

    export default {
        mixins: [
            unitTypes,
            ResponsiveMixin
        ],
        components: {
            Heading,
            Placeholder
        },
        directives: {
            sticky: VueSticky
        },
        data () {
            return {
                model: null,
                loading: {
                    visible: false
                },
                labelPosition: 'left',
                validationRules: {
                    first_name: [{
                        required: true,
                        message: this.$t('models.tenant.validation.first_name.required')
                    }],
                    last_name: [{
                        required: true,
                        message: this.$t('models.tenant.validation.last_name.required')
                    }],
                    birth_date: [{
                        required: true,
                        message: this.$t('models.tenant.validation.birth_date.required')
                    }]
                }
            }
        },
        methods: {
            getUnitType (type) {
                const {label} = this.unitTypes.find(unit => unit.type === type);

                return label
            },
            submit () {
                this.$refs.form.validate(async valid => {
                    if (!valid) {
                        return false
                    }

                    this.loading.visible = true

                    try {
                        displaySuccess(await this.$store.dispatch('updateMyTenancy', this.model))
                    } catch (error) {
                        displayError(error)
                    } finally {
                        this.loading.visible = false
                    }
                })
            }
        },
        computed: {
            breakpoints () {
                return {
                    sm: el => {
                        if (el.width <= 640) {
                            this.labelPosition = 'top'
                        } else {
                            this.labelPosition = 'left'
                        }
                    }
                }
            }
        },
        async mounted () {
            this.loading = this.$loading({
                target: this.$el.parentElement,
                text: 'Fetching your personal data...'
            });

            try {
                const {data: {
                    title,
                    company,
                    first_name,
                    last_name,
                    birth_date,
                    mobile_phone,
                    work_phone,
                    private_phone
                }} = await this.$store.dispatch('myTenancy');

                this.model = {
                    title,
                    company,
                    first_name,
                    last_name,
                    birth_date,
                    mobile_phone,
                    work_phone,
                    private_phone
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
    .personal {
        &:not(.empty):before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5d0672abb48ed.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }

        .heading {
            margin-bottom: 24px;
            
            .description {
                color: darken(#fff, 40%);
            }
        }

        .placeholder {
            font-size: 20px;
            color: darken(#F2F4F9, 32%);

            small {
                font-size: 72%;
                color: darken(#F2F4F9, 16%);
            }
        }

        .el-card {
            position: relative;
            max-width: 640px;

            .el-form {
                .el-button,
                .el-select,
                .el-date-editor {
                    width: 100%;
                }
            }
        }
    }
</style>
