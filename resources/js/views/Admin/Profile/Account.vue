<template>
    <div>
        <card>
            <el-form :model="model" label-width="96px" ref="form">
                <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                    <el-input autocomplete="off" type="text" v-model="model.name"></el-input>
                </el-form-item>
                <el-form-item :label="$t('models.user.email')" :rules="validationRules.email" prop="email">
                    <el-input autocomplete="off" type="email" v-model="model.email"></el-input>
                </el-form-item>
                <el-form-item :label="$t('models.user.phone')" prop="phone">
                    <el-input type="text" v-model="model.phone"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button @click="submit" icon="ti-save" type="primary">
                        {{$t('models.user.save')}}
                    </el-button>
                </el-form-item>
            </el-form>
        </card>
    </div>
</template>

<script>
    import {mapState, mapMutations, mapActions} from 'vuex';
    import {displayError, displaySuccess} from 'helpers/messages';
    import Card from 'components/Card';


    export default {
        components: {
            Card
        },
        data() {
            return {
                model: {
                    name: '',
                    email: '',
                    phone: ''
                },
                validationRules: {
                    email: [{
                        required: true,
                        message: this.$t("email_validation.required")
                    }, {
                        type: 'email',
                        message: this.$t("email_validation.email")
                    }],
                    name: [{
                        required: true,
                        message: this.$t("models.user.validation.name.required")
                    }]
                }
            };
        },
        methods: {
            ...mapActions(['changeDetails', 'me']),
            submit() {
                this.$refs.form.validate(async valid => {
                    if (valid) {
                        try {
                            const {data, ...message} = await this.changeDetails(this.model);

                            displaySuccess(message);


                            this.me();
                        } catch (err) {
                            displayError(err);
                        }
                    }
                });
            },
        },
        computed: {
            ...mapState(['users'])
        },
        created() {
            const {name, email, phone} = this.users.loggedInUser;

            this.model = {name, email, phone};
        },
    }
</script>

<style lang="scss" scoped>
    .el-card {
        height: 420px;
        &:before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/51675218_23843429092960149_5298969442502311936_n.png');
            background-repeat: no-repeat;
            background-size: 24em;
            background-position: calc(100% + 32px) center;
        }

        .el-form {
            max-width: 512px;

            .el-button :global([class*="ti"]) {
                margin-right: 8px;
            }
        }
    }
</style>
