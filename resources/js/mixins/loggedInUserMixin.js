import {mapGetters, mapActions, mapState} from 'vuex';

export default {
    computed: {
        ...mapGetters(['loggedInUser']),
        ...mapState({
            loggedInUser: ({users}) => {
                return users.loggedInUser;
            }
        }),

        avatar () {
            return this.loggedInUser.avatar || undefined;
        },
        username () {
            return this.loggedInUser.name || '';
        }
    }
};
