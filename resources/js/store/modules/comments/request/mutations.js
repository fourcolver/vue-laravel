export default {
    SET_REQUEST_COMMENTS (state, {requestId, inserting, ...payload}) {
        payload.data = payload.data.reverse();

        if (inserting) {
            let request = state[requestId];
            let props = Object.keys(payload).filter(prop => prop !== 'data');

            for (let prop of props) {
                if (request[prop] !== payload[prop]) {
                    request[prop] = payload[prop];
                }
            }

            request.data = [...payload.data, ...request.data];
        } else {
            state[requestId] = {...state[requestId], ...payload};
        }
    },
    SAVE_REQUEST_COMMENT (state, {requestId, ...payload}) {
        state[requestId].total++;
        state[requestId].data.push(payload);
    },
    UPDATE_REQUEST_COMMENT (state, {requestId, ...payload}) {
        let comment = state[requestId].data.find(c => c.id === payload.id);

        Object.assign(comment, payload);
    }
};
