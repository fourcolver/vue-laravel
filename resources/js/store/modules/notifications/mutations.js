export default {
    SET (state, {inserting, ...payload}) {
        if (inserting) {
            let props = Object.keys(payload).filter(prop => prop !== 'data');

            for (let prop of props) {
                if (state[prop] !== payload[prop]) {
                    state[prop] = payload[prop];
                }
            }

            state.data = [...state.data, ...payload.data];
        } else {
            Object.assign(state, payload);
        }
    },
    SET_MARKED_AS_READ (state, payload) {
        if (!payload) {
            for (let notification of state.data) {
                if (!notification.read_at) {
                    notification.read_at = Date.now();
                }
            }
        } else {
            const notification = state.data.find(notification => notification.id === payload);

            notification.read_at = Date.now();
        }
    }
};
