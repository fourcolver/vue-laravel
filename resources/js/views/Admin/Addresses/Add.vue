<template>
    <div class="users-add">
        <heading icon="ti-home" :title="$t('models.address.add')" shadow="heavy" class="custom-heading"/>
        <el-form :model="newAddress" ref="newAddressForm" label-position="top">
            <el-row>
                <el-col :span="12">
                    <el-form-item :label="$t('models.address.state.label')" prop="state_id" :rules="validationRules.state_id">
                        <el-select v-model="newAddress.state_id" :placeholder="$t('models.address.state.label')">
                            <el-option :label="state.name" :value="state.id" :key="state.id" v-for="state in getStates"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="city" :label="$t('models.address.city')" :rules="validationRules.city">
                        <el-input type="text" v-model="newAddress.city" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="street" :label="$t('models.address.street')" :rules="validationRules.street">
                        <el-input type="text" v-model="newAddress.street" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="street_nr" :label="$t('models.address.street_nr')" :rules="validationRules.street_nr">
                        <el-input type="text" v-model="newAddress.street_nr" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="zip" :label="$t('models.address.zip')" :rules="validationRules.zip">
                        <el-input type="text" v-model="newAddress.zip" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" icon="ti-save" @click="submitNewAddressForm()">
                            {{$t('models.address.save')}}
                        </el-button>
                    </el-form-item>
                </el-col>
            </el-row>
        </el-form>
    </div>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Heading from 'components/Heading';

    export default {
        name: 'AdminAddressesAdd',
        components: {
            Heading
        },
        data() {
            return {
                validationRules: {
                    state_id: [
                        {
                            required: true,
                            message: this.$t('models.address.validation.state.required')
                        }
                    ],
                    city: [
                        {
                            required: true,
                            message: this.$t('models.address.validation.city.required')
                        }
                    ],
                    street: [
                        {
                            required: true,
                            message: this.$t('models.address.validation.street.required')
                        }
                    ],
                    street_nr: [
                        {
                            required: true,
                            message: this.$t('models.address.validation.street_nr.required')
                        }
                    ],
                    zip: [
                        {
                            required: true,
                            message: this.$t('models.address.validation.zip.required')
                        }
                    ]
                },
                newAddress: {
                    state_id: null,
                    city: '',
                    street: '',
                    street_nr: '',
                    zip: '',
                }
            }
        },
        created() {
            this.fetchStates();
        },
        computed: {
          ...mapGetters(['getStates'])
        },
        methods: {
            ...mapActions(['createAddress', 'fetchStates']),
            submitNewAddressForm() {
                this.$refs.newAddressForm.validate((valid) => {
                    if (valid) {
                        this.createAddress(this.newAddress).then((response) => {
                            displaySuccess(response);
                            this.resetForm();
                        }).catch((err) => {
                            displayError(err);
                        });
                    } else {
                        return false;
                    }
                });
            },
            resetForm() {
                this.$refs.newAddressForm.resetFields();
            }
        }
    }
</script>

<style lang="scss" scoped>
    .users-add {
        .custom-heading {
            margin-bottom: 2em;
        }

        .el-row {
            .el-col:first-of-type {
                max-width: 768px;

                .el-button :global(i) {
                    margin-right: 8px;
                }

                .el-select {
                    width: 100%;;
                }
            }
        }
    }
</style>