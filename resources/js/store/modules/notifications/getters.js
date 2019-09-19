export default {
    get: state => state,
    unread (state) {
        return state.data.filter(notification => !notification.read_at);
    }
};
