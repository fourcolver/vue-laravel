<template>
    <div class="users-add">
        <heading :title="$t('models.user.add')" icon="ti-user" shadow="heavy">
            <add-actions :saveAction="submit" route="adminUsers" editRoute="adminUsersEdit"/>
        </heading>
        <div class="crud-view">
            <card :loading="loading">
                <el-form :model="model" label-width="192px" ref="form" style="max-width: 512px;">
                    <el-form-item :label="$t('models.user.name')" :rules="validationRules.name" prop="name">
                        <el-input type="text" v-model="model.name"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.email')" :rules="validationRules.email" prop="email">
                        <el-input type="email" v-model="model.email"/>
                    </el-form-item>
                    <el-form-item :label="$t('password')" :rules="validationRules.password" prop="password">
                        <el-input type="password" v-model="model.password"/>
                    </el-form-item>
                    <el-form-item :label="$t('confirm_password')" :rules="validationRules.password_confirmation"
                                  prop="password_confirmation">
                        <el-input type="password" v-model="model.password_confirmation"/>
                    </el-form-item>
                    <el-form-item :label="$t('models.user.phone')" prop="phone">
                        <el-input type="text" v-model="model.phone"/>
                    </el-form-item>
                    <el-form-item :label="$t('roles.label')" :rules="validationRules.role" prop="role">
                        <el-select style="width: 100%;" v-model="model.role">
                            <el-option :key="role" :label="$t('roles.' + role )" :value="role" v-for="role in allRoles"/>
                        </el-select>
                    </el-form-item>
                </el-form>
            </card>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Card from 'components/Card';
    import AdminUsersMixin from 'mixins/adminUsersMixin';
    import AddActions from 'components/EditViewActions';

    export default {
        name: 'AdminUsersAdd',
        mixins: [AdminUsersMixin({
            mode: 'add'
        })],
        components: {
            Heading,
            Card,
            AddActions
        },
        created() {
            this.model.role = this.$route.params.role;
        }
    }
</script>

<style lang="scss" scoped>
    .users-add {
        .heading {
            margin-bottom: 20px;
        }
    }
</style>
