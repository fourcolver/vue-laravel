<template>
    <el-popover class="user" width="384" trigger="click" placement="bottom" popper-class="user" :popper-options="popperOptions" @show="onShow" v-if="loggedInUser">
        <notifications @onMoreLoaded="onShow"/>
        <div class="container" slot="reference">
            <el-badge :max="9" :hidden="hiddenCounter" :value="loggedInUser.unread_notifications_count">
                <avatar :size="40" :src="loggedInUser.avatar" :name="loggedInUser.name" />
            </el-badge>
            <div class="content">
                {{ loggedInUser.name }}
                <small>{{ loggedInUser.email }}</small>
            </div>
        </div>
    </el-popover>
</template>

<script>
    import Notifications from './UserNotifications'
    import Avatar from 'components/Avatar'
    import {displayError, displaySuccess} from 'helpers/messages'
    import {mapGetters} from 'vuex'

    export default {
        components: {
            Avatar,
            Notifications
        },
        data () {
            return {
                hiddenCounter: true,
                popperOptions: {
                    gpuAcceleration: true
                }
            }
        },
        computed: {
            ...mapGetters(['loggedInUser'])
        },
        methods: {
            async onShow() {
                if (this.$store.getters['notifications/unread'].length) {
                    try {
                        await this.$store.dispatch('notifications/markAsRead');

                        this.hiddenCounter = true; // ? quite wrong, with store and everything, needs more work, too tired now
                    } catch (err) {
                        displayError(err);
                    }
                }
            }
        },
        created () {
            if (this.loggedInUser.unread_notifications_count) {
                this.hiddenCounter = false;
            }
        }
    }
</script>

<style lang="scss">
    .el-popper.user {
        padding: 0;
        border-radius: 6px;
        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
    }
</style>

<style lang="scss" scoped>
    .user .container {
        display: flex;
        align-items: center;
        .content {
            margin-left: 16px;
            line-height: 1.48;
            small {
                display: block;
            }
        }
        .el-badge :global(.el-badge__content) {
            top: 16px;
            right: 16px;
        } 
    }
</style>
