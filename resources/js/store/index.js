import Vue from 'vue'
import Vuex from 'vuex'

//modules
import users from './modules/users';
import tenants from './modules/tenants';
import buildings from './modules/buildings';
import units from './modules/units';
import addresses from './modules/addresses';
import services from './modules/services';
import posts from './modules/posts';
import * as comments from './modules/comments';
import districts from './modules/districts';
import requests from './modules/requests';
import requestCategories from './modules/requestCategories';
import propertyManagers from './modules/propertyManagers';
import products from './modules/products';
import application from './modules/application';
import notifications from './modules/notifications';
import likes from './modules/likes';
import products2 from './modules/products2';
import media from './modules/media'
import posts2 from './modules/posts2'
import templates from './modules/templates'
import cleanify from './modules/cleanify'

Vue.use(Vuex);

// TBD in future
const {post, request, product, ...restcomments} = comments;

const state = {
    token: null,
    sidebarLevel: {
        level: 1,
        direction: 0,
        maxLevel: 1
    },
    allLanguages: [
        "en", "fr", "de", "it"
    ]
};

const getters = {
    getSidebarLevel(state) {
        return state.sidebarLevel;
    },
    getAllAvailableLanguages(state) {
        return state.allLanguages;
    }
};

const mutations = {
    SET_TOKEN(state, token) {
        state.token = token
    },
    REMOVE_TOKEN(state) {
        state.token = null
    },
    CHANGE_SIDEBAR_LEVEL(state, level) {
        document.body.className = document.body.className.replace(/level-[0-9]/g, "");
        document.body.className += ' level-' + level.level;
        state.sidebarLevel = level;
    },
};

const actions = {
    changeSidebarMenuLevel({state, commit}) {
        let currentLevel = state.sidebarLevel;
        if (currentLevel.direction) {
            currentLevel.level += 1;
            if (currentLevel.level == currentLevel.maxLevel) {
                currentLevel.direction = 0;
            }
        } else {
            currentLevel.level -= 1;
            if (currentLevel.level == 0) {
                currentLevel.direction = 1;
            }
        }

        commit('CHANGE_SIDEBAR_LEVEL', currentLevel);
    },
    setSidebarMenuMaxLevel({state, commit}, payload) {
        commit('CHANGE_SIDEBAR_LEVEL', payload);
    },
};


const store = new Vuex.Store({
    state,
    mutations,
    actions,
    getters,
    modules: {
        users,
        tenants,
        buildings,
        units,
        addresses,
        services,
        posts,
        comments: {
            namespaced: true,
            ...restcomments,
            modules: {
                post: {
                    namespaced: true,
                    ...post
                },
                request: {
                    namespaced: true,
                    ...request
                },
                product: {
                    namespaced: true,
                    ...product
                }
            }
        },
        districts,
        requests,
        requestCategories,
        propertyManagers,
        products,
        templates,
        cleanify,
        application: {
            namespaced: true,
            ...application
        },
        notifications: {
            namespaced: true,
            ...notifications
        },
        likes: {
            namespaced: true,
            ...likes
        },
        products2: {
            namespaced: true,
            ...products2
        },
        media: {
            namespaced: true,
            ...media
        },
        posts2: {
            namespaced: true,
            ...posts2
        }
    }
});


export default store;
