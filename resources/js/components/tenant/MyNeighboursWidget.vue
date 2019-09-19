<template>
    <card class="my-neighbours-widget">
        <template slot="header">My neighbours</template>
        <template v-if="isLoading">
            <loader visible centered :size="20" />
        </template>
        <template v-else>
            <ul v-if="tenants.length">
                <li v-for="(tenant, idx) in tenants" :key="idx">
                    <avatar
                        :size="36"
                        :src="tenant.user.avatar"
                        :name="tenant.user.name"
                    />
                    <div class="content">
                        {{ tenant.full_name }}
                        <small>{{ tenant.user.phone }}</small>
                    </div>
                </li>
            </ul>
            <placeholder v-else :src="require('img/5c9e682f7dad6.png')">
                You have no neighbours...
                <small slot="secondary">All of your neighbours will be listed here...</small>
            </placeholder>
        </template>
    </card>
</template>

<script>
    import Card from 'components/Card'
    import Avatar from 'components/Avatar'
    import Loader from 'components/SimpleLoader'
    import Placeholder from 'components/Placeholder'
    import {displaySuccess, displayError} from 'helpers/messages'

    export default {
        components: {
            Card,
            Avatar,
            Loader,
            Placeholder
        },
        data () {
            return {
                loading: false,
                tenants: []
            }
        },
        computed: {
            isLoading () {
                return this.loading && !this.tenants.length;
            }
        },
        async created () {
            const {tenant} = this.$store.getters.loggedInUser

            if (tenant && tenant.building_id) {
                try {
                    this.loading = true

                    const {tenants} = await this.$store.dispatch('getBuilding', {
                        id: tenant.building_id
                    })

                    this.tenants = tenants.filter(({id}) => id !== this.$store.getters.loggedInUser.tenant.id).map(tenant => {
                        let name = tenant.first_name + ' ' + tenant.last_name

                        if (!tenant.company) {
                            name = tenant.title.charAt(0).toUpperCase() + tenant.title.slice(1) + '. ' + name
                        }

                        tenant.full_name = name

                        return tenant
                    })
                } catch (err) {
                    displayError(err)
                } finally {
                    this.loading = false
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card.my-neighbours-widget {
        :global(.el-card__body) {
            ul {
                color: #000;
                text-align: left;
                list-style: none;
                padding: 0;
                margin: -16px;
                li {
                    font-size: 14px;
                    display: flex;
                    align-items: center;
                    padding: .5em 1em;
                    .content {
                        display: flex;
                        flex-direction: column;
                        font-size: 15px;
                        margin-left: 1em;
                        small {
                            color: darken(#fff, 48%);
                        }
                    }
                    &:not(:last-of-type) {
                        border-bottom: 1px darken(#fff, 8%) solid;
                    }
                }
            }
        }
    }
</style>