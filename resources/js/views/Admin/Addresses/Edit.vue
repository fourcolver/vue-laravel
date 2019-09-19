<template>
    <div class="users-edit">
        <heading icon="ti-user" :title="$t('models.address.edit')" shadow="heavy" class="custom-heading"/>
        <el-form :model="editAddress" ref="editAddressForm" label-position="top">
            <el-row :gutter="20">
                <el-col :span="12">
                    <el-form-item :label="$t('models.address.state.label')" prop="state_id" :rules="validationRules.state_id">
                        <el-select v-model="editAddress.state_id" :placeholder="$t('models.address.state.label')">
                            <el-option :label="state.name" :value="state.id" :key="state.id" v-for="state in getStates"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item prop="city" :label="$t('models.address.city')" :rules="validationRules.city">
                        <el-input type="text" v-model="editAddress.city" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="street" :label="$t('models.address.street')" :rules="validationRules.street">
                        <el-input type="text" v-model="editAddress.street" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="street_nr" :label="$t('models.address.street_nr')" :rules="validationRules.street_nr">
                        <el-input type="text" v-model="editAddress.street_nr" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item prop="zip" :label="$t('models.address.zip')" :rules="validationRules.zip">
                        <el-input type="text" v-model="editAddress.zip" autocomplete="off"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="primary" icon="ti-save" @click="submitEditAddressForm()">
                            {{$t('models.user.save')}}
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
        name: 'AdminAddressesEdit',
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
                editAddress: {
                    state_id: null,
                    city: '',
                    street: '',
                    street_nr: '',
                    zip: '',
                }
            }
        },
        created() {
            this.fetchStates().then(() => {
                this.getAddress({id: this.$route.params.id}).then((address) => {
                    this.editAddress = address;
                }).catch((error) => {
                    displayError(error);
                });
            });

        },
        computed: {
            ...mapGetters(['getStates'])
        },
        methods: {
            ...mapActions(['updateAddress', 'getAddress', 'fetchStates']),
            submitEditAddressForm() {
                this.$refs.editAddressForm.validate((valid) => {
                    if (valid) {
                        let editedAddress = this.editAddress;

                        this.updateAddress(editedAddress).then((response) => {
                            displaySuccess(response);
                        }).catch((err) => {
                            displayError(err);
                        });
                    } else {
                        return false;
                    }
                });
            },
        }
    }
</script>

<style lang="scss" scoped>
    .users-edit {
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