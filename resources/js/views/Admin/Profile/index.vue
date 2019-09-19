<template>
    <div class="profile">
        <heading :title="$t('pages.profile.pageTitle')" class="custom-heading" icon="ti-user" shadow="heavy" />
        <div class="crud-view">
            <el-tabs v-model="active">
                <el-tab-pane :label="$t('pages.profile.profile')" :lazy="true" name="profile">
                    <profile/>
                </el-tab-pane>
                <el-tab-pane :label="$t('pages.profile.account')" :lazy="true" name="account">
                    <accounts/>
                </el-tab-pane>
                <el-tab-pane :label="$t('pages.profile.security')" :lazy="true" name="security">
                    <security/>
                </el-tab-pane>
                <el-tab-pane :label="$t('pages.profile.notifications')" :lazy="true" name="notifications">
                    <notifications/>
                </el-tab-pane>
            </el-tabs>
        </div>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Profile from './Profile';
    import Accounts from './Account';
    import Security from './Security';
    import Notifications from './Notifications';


    export default {
        name: 'AdminSettings',
        components: {
            Heading,
            Profile,
            Accounts,
            Security,
            Notifications
        },
        data() {
            return {
                active: 'profile'
            }
        }
    }
</script>

<style lang="scss" scoped>
    .profile {
        height: 77vh !important;

        .custom-heading {
            margin-bottom: 2em;
        }

        .el-tabs {
            .el-tab-pane {
                padding: 2px;
            }

            // :global(.el-tabs__header) {
            //     background-color: #fff;
            //     box-shadow: 0 1px 3px transparentize(#000, .88),
            //                 0 1px 2px transparentize(#000, .76);
            // }
        }
    }
</style>
