<template>
    <el-container class="admin-layout" direction="vertical">
        <a-header>
            <router-link :to="{name: ''}" class="header-link">
                <div  v-bind:class="[{ active: showMenu }, language]">
                    <div class="language-iconBorder" @click="toggleShow">
                        <div class="language-checked-img">
                            <span v-bind:class="selectedFlag" id="flagIcon"></span>
                        </div>
                    </div>
                    <div class="language-check-box">
                        <div class="language-check-box-title">
                            {{$t('chooseLanguage')}}
                        </div>
                        <div class="language-check-box-body">
                            <ul class="language-check-box-body-item" v-for='language in this.languages' @click='itemClicked(language.symbol, language.flag)'>
                                <li>
                                    <span  v-bind:class="language.flag"></span>
                                    <p>{{language.name}}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </router-link>


            <el-menu class="dropdown-menu" menu-trigger="hover" mode="horizontal">
                <el-submenu index="2">
                    <template slot="title">

                        <div class="user-params">
                            <div class="user-params-img" :style="`background-image: url('${user.avatar}')`"></div>

                            <div class="user-params-wrap">
                                <span class="user-params-name">{{userName.slice(0, 8)}}
                                    <span v-if="userName.length > 10">...</span>
                                </span>
                                <i class="el-submenu__icon-arrow el-icon-arrow-down user-params-wrap-icon"></i>
                            </div>
                        </div>


                    </template>
                    <el-menu-item index="2-1" class="el-menu-item-d">
                        <router-link :to="{name: 'adminProfile'}" class="el-menu-item-link">
                            <i class="ti-user"/>
                            {{$t('menu.profile')}}
                        </router-link>
                    </el-menu-item>
                    <el-menu-item index="2-2" class="el-menu-item-d">
                        <template v-if="$can($permissions.view.realEstate)">
                            <router-link :to="{name: 'adminSettings'}" class="el-menu-item-link">
                                <i class="ti-settings"/>
                                {{$t('menu.settings')}}
                            </router-link>
                        </template>
                    </el-menu-item>
                    <el-menu-item index="2-3">
                        <el-button @click="handleLogout" type="text">
                            <div class="logout-button">
                                <i class="ti-power-off"/>
                                {{$t('menu.logout')}}
                            </div>
                        </el-button>
                    </el-menu-item>
                </el-submenu>
            </el-menu>



        </a-header>
        <el-container>
            <a-sidebar :links="links">
            </a-sidebar>
            <el-main sticky-container>
                <v-router-transition transition="slide-left">
                    <router-view/>
                </v-router-transition>
                <a-footer />
            </el-main>
        </el-container>
    </el-container>
</template>

<script>
    import AHeader from 'components/AdminHeader';
    import ASidebar from 'components/AdminSidebar';
    import AFooter from 'components/AdminFooter';
    import VRouterTransition from 'v-router-transition';
    import {mapActions, mapState} from "vuex";

    export default {
        name: 'AdminLayout',
        components: {
            AHeader,
            ASidebar,
            AFooter,
            VRouterTransition
        },

        data() {
            return {
                fullScreenText: 'Enter fullscreen mode',
                showMenu: false,
                language: "language",
                activeLanguage: 'Piano',
                selectedFlag: '',

                activeIndex: '1',
                activeIndex2: '1',

                userName: null,

                languages: [
                    {name: 'Français', symbol: 'fr', flag: 'flag-icon flag-icon-fr'},
                    {name: 'Italiano', symbol: 'it', flag: 'flag-icon flag-icon-it'},
                    {name: 'Deutsch', symbol: 'de', flag: 'flag-icon flag-icon-de'},
                    {name: 'English', symbol: 'en', flag: 'flag-icon flag-icon-us'}
                ]
            }
        },

        computed: {
            ...mapState({
                user: ({users}) => users.loggedInUser
            }),
            links() {
                return [{
                    icon: 'icon-chart-bar',
                    title: 'Dashboard',
                    route: {
                        name: 'adminDashboard'
                    }
                }, {
                    icon: 'icon-commerical-building',
                    title: this.$t('menu.buildings'),
                    permission: this.$permissions.list.user,
                    children: [{
                        title: this.$t('menu.all_buildings'),
                        permission: this.$permissions.list.building,
                        route: {
                            name: 'adminBuildings'
                        }
                    }, {
                        title: this.$t('menu.units'),
                        permission: this.$permissions.list.unit,
                        route: {
                            name: 'adminUnits'
                        }
                    }, {
                        title: this.$t('menu.districts'),
                        permission: this.$permissions.list.district,
                        route: {
                            name: 'adminDistricts'
                        }
                    }]
                }, {
                    icon: 'icon-chat-empty',
                    title: this.$t('menu.requests'),
                    permission: this.$permissions.list.request,
                    children: [{
                        title: this.$t('menu.all_requests'),
                        permission: this.$permissions.list.request,
                        route: {
                            name: 'adminRequests'
                        }
                    }, {
                        title: this.$t('menu.activity'),
                        nestedItem: true,
                        permission: this.$permissions.list.audit,
                        route: {
                            name: 'adminRequestsActivity'
                        }
                    }]
                }, {
                    title: this.$t('menu.tenants'),
                    icon: 'icon-group',
                    permission: this.$permissions.list.tenant,
                    route: {
                        name: 'adminTenants'
                    }
                }, {
                    icon: 'icon-users',
                    title: this.$t('menu.propertyManagers'),
                    permission: this.$permissions.list.propertyManager,
                    route: {
                        name: 'adminPropertyManagers'
                    }
                }, {
                    icon: 'icon-tools',
                    title: this.$t('menu.services'),
                    permission: this.$permissions.list.provider,
                    route: {
                        name: 'adminServices'
                    }
                }, {
                    title: this.$t('menu.posts'),
                    icon: 'icon-megaphone-1',
                    permission: this.$permissions.list.post,
                    route: {
                        name: 'adminPosts'
                    }
                }, {
                    title: this.$t('menu.products'),
                    icon: 'icon-basket',
                    permission: this.$permissions.list.product,
                    route: {
                        name: 'adminProducts'
                    }
                }, {
                    icon: 'icon-user',
                    title: this.$t('menu.users'),
                    permission: this.$permissions.list.user,
                    children: [{
                        title: this.$t('menu.admins'),
                        route: {
                            name: 'adminUsers',
                            query: {
                                role: 'administrator'
                            }
                        }
                    }, {
                        title: this.$t('menu.super_admins'),
                        route: {
                            name: 'adminUsers',
                            query: {
                                role: 'super_admin'
                            }
                        }
                    }]
                }];
            }
        },

        methods: {
            ...mapActions(['logoutAdmin']),
            ...mapActions(['updateSettings']),

            toggleFullscreen() {
                if (document.fullscreenElement) {
                    this.fullScreenText = 'Enter fullscreen mode';

                    document.exitFullscreen();
                } else {
                    this.fullScreenText = 'Exit fullscreen mode';

                    document.documentElement.requestFullscreen();
                }
            },

            handleLogout() {
                this.$confirm('You will be logged out.', 'Are you sure?', {
                    type: 'warning'
                }).then(() => {
                    //this.$router.push({name: 'login'});
                    this.logoutAdmin()
                        .then(() => {
                            this.$router.push({name: 'login'});
                        })
                        .catch(err => {
                            displayError(err);
                        });
                }).catch(() => {

                });
            },

            toggleHide: function(event) {
                console.log(event);
                if(event.target.id === 'flagIcon') {
                    
                } else {
                    if(this.showMenu) {
                        this.showMenu = !this.showMenu;
                    }
                }
            },

            toggleShow: function() {
                this.showMenu = !this.showMenu;
            },

            itemClicked: function(item, flag) {
                // this.toggleShow();
                this.onClick(item, flag);
            },

            changeLanguage: function(language) {
                this.activeLanguage = language;
            },

            onClick(language, flag){
                this.$i18n.locale = language;
                this.selectedFlag = flag;

                console.log('language --- ', this.$i18n.locale);

                this.toggleShow();

                this.saveLangParamsInLocalStorage();
            },

            init(){
                if(!localStorage.getItem('locale')){
                    if(this.user.settings.language === 'en'){
                        this.selectedFlag = `flag-icon flag-icon-us`;
                    }else {
                        this.selectedFlag = `flag-icon flag-icon-${this.user.settings.language}`;
                    }
                } else {
                    this.selectedFlag = localStorage.getItem('selectedFlag');
                    this.$i18n.locale = localStorage.getItem('locale');
                }
            },

            saveLangParamsInLocalStorage(){
                localStorage.setItem('locale', this.$i18n.locale);
                localStorage.setItem('selectedFlag', this.selectedFlag);
            },
        },

        mounted(){
            this.init();

            this.$store.subscribe((mutation, state) => {
                if(mutation.type === "SET_LOGGED_IN_USER"){

                    if(this.user.settings.language === 'en'){
                        this.selectedFlag = `flag-icon flag-icon-us`;
                    }else {
                        this.selectedFlag = `flag-icon flag-icon-${mutation.payload.settings.language}`;
                    }
                }
            });
        },

        created() {
            this.userName = this.user.name;
            document.onclick = this.toggleHide;
        }


    }
</script>


<style lang="scss" scoped>
    .el-container {
        background-color: #F2F4F9;
        height: 100%;

        .el-main {
            padding: 0;
            height: 100%;
            overflow: hidden;
            flex-basis: 0;
            overflow-y: auto;

            display: flex;
            flex-direction: column;
            justify-content: space-between;

            .el-breadcrumb {
                background-color: #fff;
                padding: 1em;
                margin: -30px;
                margin-bottom: 2em;
                box-shadow: 0 1px 3px transparentize(#000, .88),
                0 1px 2px transparentize(#000, .76);
                position: relative;
                top: -2px;
            }

            // causes a bug
            // > * {
            //     height: 100% !important;
            // }

        }

        .user-params{
            display: flex;
            align-items: center;
            position: relative;
            width: 100%;

            &-img{
                width: 33px;
                height: 33px;
                border: solid #c2c2c2 1px;
                border-radius: 50%;
            }

            &-wrap{
                display: flex;
                align-items: center;
                padding-left: 15px;

                &-icon{
                    position: static;
                    margin-top: 0;
                    margin-left: 10px;
                }
            }

            &-name{
                display: flex;
                width: auto;

                &-rotateIcon{
                    transform: rotate(180deg);
                }
            }
        }

        .dropdown{
            position: absolute;
            width: 106%;
            top: 56px;
            left: 0px;

            &-list{
                list-style: none;
                background: #fff;
                width: 100%;
                padding: 0 10px 10px 10px;
                margin: 0;
                box-shadow: -5px 4px 6px -5px;
                border-bottom-left-radius: 5px;
                overflow: hidden;
            }
        }


        .language{
            position: relative;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 30px;

            &:after{
                content: "";
                position: absolute;
                right: -15px;
                height: 90%;
                width: 1px;
                background: #c2c2c2;;
            }


            &.active{
                background: #ececec;


                .language-check-box{
                    top: 45px;
                    pointer-events: auto;
                    opacity: 1;
                }
            }

            &-iconBorder{
                width: 35px;
                height: 35px;
                border-radius: 50%;
                background: #eee;
                display: flex;
                justify-content: center;
                align-items: center;
                transition: 0.2s ease-in;

                &:hover{
                    background: #B4B4B4;
                }
            }

            .language-checked-img{
                width: 25px;
                height: 25px;
                border-radius: 50%;
                overflow: hidden;
                position: relative;

                span{
                    width: 100%;
                    height: 100%;
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    object-fit: cover;
                    display: block;
                    background-size: cover;
                }
            }

            .language-check-box{
                position: absolute;
                top: 25px;
                left: -70px;
                z-index: 5;
                background: white;
                box-shadow: 0 2px 5px rgba(34,34,34,.4);
                border-radius: .4rem;
                overflow: hidden;
                opacity: 0;
                pointer-events: none;
                transition: .2s;

                .language-check-box-title{
                    padding: 15px 30px;
                    background: #525560;
                    color: #fff;
                    cursor: default !important;
                }

                .language-check-box-body-item{
                    padding: 0;
                    margin: 0;

                    li{
                        display: flex;
                        justify-content: flex-start;
                        align-items: center;
                        padding: 10px 20px;
                        transition: .4s;


                        &:hover{
                            background-color: #f0f9f1;
                        }

                        span{
                            margin: 0 20px 0 0 ;
                        }

                        p{
                            margin: 0;
                            color: #303133;
                        }
                    }
                }
            }

        }
    }
</style>
