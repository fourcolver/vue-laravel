<template>
    <div class="users-edit">
        <heading :title="$t('models.user.edit')" icon="ti-user">
            <edit-actions :saveAction="submit" :deleteAction="deleteUser" route="adminUsers" shadow="heavy"/>
        </heading>
        <el-row class="crud-view">
            <el-col :md="12">
                <card :loading="loading">
                    <el-form :model="model" label-position="top" label-width="192px" ref="form">
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                                    <el-input type="text" v-model="model.name"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.email')" :rules="validationRules.email"
                                              prop="email">
                                    <el-input type="email" v-model="model.email"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('password')" :rules="validationRules.password"
                                              autocomplete="off"
                                              prop="password">
                                    <el-input type="password" v-model="model.password"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('confirm_password')"
                                              :rules="validationRules.password_confirmation"
                                              prop="password_confirmation">
                                    <el-input type="password" v-model="model.password_confirmation"/>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        <el-row :gutter="20">
                            <el-col :md="12">
                                <el-form-item :label="$t('models.user.phone')" prop="phone">
                                    <el-input type="text" v-model="model.phone"/>
                                </el-form-item>
                            </el-col>
                            <el-col :md="12">
                                <el-form-item :label="$t('roles.label')" :rules="validationRules.role" prop="role">
                                    <el-select style="width: 100%;" v-model="model.role">
                                        <el-option :key="role" :label="$t('roles.' + role )" :value="role"
                                                   v-for="role in allRoles"/>
                                    </el-select>
                                </el-form-item>
                            </el-col>
                        </el-row>
                    </el-form>
                </card>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import {mapActions} from 'vuex';
    import AdminUsersMixin from 'mixins/adminUsersMixin';
    import EditActions from 'components/EditViewActions';

    export default {
        name: 'AdminUsersEdit',
        mixins: [AdminUsersMixin({
            mode: 'edit'
        })],
        components: {
            Heading,
            Card,
            EditActions
        },
        methods: {
            ...mapActions(['deleteUser']),
        }
    }
</script>

<style lang="scss" scoped>
    .users-edit {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>

