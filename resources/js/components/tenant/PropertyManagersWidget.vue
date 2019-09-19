<template>
    <card class="property-managers-widget">
        <template slot="header">Property managers</template>
        <template v-if="isLoading">
            <loader visible centered :size="20" />
        </template>
        <template v-else>
            <el-carousel :autoplay="false" :arrow="managers.length > 1 ? 'hover': 'never'" height="256" v-if="managers.length">
                <el-carousel-item v-for="manager in managers" :key="manager.id">
                    <avatar :size="100" :src="manager.user.avatar" :name="manager.user.name" />
                    <div class="title">
                        {{manager.user.name}}
                        <small>{{manager.user.email}}</small>
                        <small>{{manager.user.phone}}</small>
                    </div>
                    <div class="slogan">
                        {{manager.slogan}}
                    </div>
                </el-carousel-item>
            </el-carousel>
            <placeholder v-else :src="require('img/5cae67303cdad.png')">
                There are no property managers...
                <small slot="secondary">Every property manager will be list here...</small>
            </placeholder>
        </template>
    </card>
</template>

<script>
    import Card from 'components/Card'
    import Avatar from 'components/Avatar'
    import Loader from 'components/SimpleLoader'
    import Placeholder from 'components/Placeholder'

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
                managers: []
            }
        },
        computed: {
            isLoading () {
                return this.loading && !this.managers.length;
            }
        },
        async created () {
            const {tenant} = this.$store.getters.loggedInUser

            if (tenant && tenant.building_id) {
                try {
                    this.loading = true

                    const {managers} = await this.$store.dispatch('getBuilding', {
                        id: tenant.building_id
                    })

                    if (managers) {
                        this.managers = managers;
                    }
                } catch (err) {
                    displayError(err)
                } finally {
                    this.loading = false
                }
            }
        }
    }
</script>

<style lang="scss">
    .el-card.property-managers-widget {
        :global(.el-card__body) {
            .el-carousel {
                :global(.el-carousel__item) {
                    display: flex;
                    flex-direction: column;
                    align-content: center;
                    justify-content: center;
                    text-align: center;
                    :global(.avatar) {
                        margin: .5em auto;
                    }
                    .title {
                        font-size: 18px;
                        line-height: 1.48;
                        small {
                            font-size: 72%;
                            display: block;
                            font-weight: normal;
                            color: darken(#fff, 48%);
                        }
                    }
                    .slogan {
                        color: darken(#fff, 40%);
                        margin-top: .5em;
                        overflow: hidden;
                        display: -webkit-box;
                        -webkit-line-clamp: 3;
                        -webkit-box-orient: vertical;
                    }
                }
            }
        }
    }
</style>