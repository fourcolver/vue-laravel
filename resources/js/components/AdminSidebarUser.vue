<template>
    <div class="user">
        <div class="content">
            <div class="image">
                <img :src="avatar"/>
            </div>
            <h1 class="title">
                {{ loggedInUser.name }}
                <small>({{ loggedInUser.email }})</small>
            </h1>
        </div>
    </div>
</template>

<script>
    import {mapGetters, mapState} from 'vuex';

    export default {
        name: 'AdminSidebarUser',
        computed: {
            ...mapState({
                loggedInUser: ({users}) => {
                    return users.loggedInUser;
                }
            }),
            ...mapGetters(["getAllAvailableLanguages", "loggedInUser"]),
            avatar() {
                if (this.loggedInUser.avatar) {
                    return `/${this.loggedInUser.avatar}?${Date.now()}`;
                }
            }
        }
    }
</script>

<style lang="scss" scoped>
    .user {
        position: relative;
        background-color: #000;
        height: 180px;

        &:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('~img/50922917_23843182402600615_4749999182427717632_n.png');
            background-size: cover;
            background-repeat: no-repeat;
            filter: opacity(.72);
        }

        .content {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;

            .image {
                position: relative;
                top: 20px;

                img {
                    width: 80px;
                    height: 80px;
                    object-fit: cover;
                    border-radius: 50%;
                    box-shadow: 0 1px 3px transparentize(#000, .88),
                    0 1px 2px transparentize(#000, .76);
                    border: 4px transparentize(#fff, .72) solid;
                    position: relative;
                    z-index: 1;
                }

                &:before {
                    content: '';
                    width: 80px;
                    height: 80px;
                    border-radius: 50%;
                    position: absolute;
                    top: 0;
                    left: 0;
                    margin: 4px;
                    background-color: #fff;
                    z-index: 0;
                }
            }

            .title {
                font-size: 1.32em;
                text-align: center;

                small {
                    display: block;
                    font-size: 72%;
                    font-weight: normal;
                    color: darken(#fff, 12%);
                }
            }

            .el-row {
                width: 100%;
                height: 64px;
                position: absolute;
                bottom: 0;

                .el-col {
                    text-align: center;
                    transition: color .16s linear;
                    cursor: pointer;

                    :global(.el-button) {
                        color: #fff;

                        i {
                            display: block;
                            font-size: 18px;
                            margin-bottom: .5em;
                        }
                    }

                    &:hover :global(.el-button) {
                        color: #409EFF;
                    }
                }
            }
        }
    }
</style>
