export default {
    SET_POST_COMMENTS(state, {postId, inserting, ...payload}) {
        if (inserting) {
            let post = state[postId];
            let props = Object.keys(payload).filter(prop => prop !== 'data');

            for (let prop of props) {
                if (post[prop] !== payload[prop]) {
                    post[prop] = payload[prop];
                }
            }

            post.data = [...payload.data, ...post.data];
        } else {
            state[postId] = {...state[postId], ...payload};
        }
    },
    SAVE_POST_COMMENT(state, {postId, ...payload}) {
        state[postId].total++;
        state[postId].data.push(payload);
    },
    UPDATE_POST_COMMENT(state, {postId, ...payload}) {
        let comment = state[postId].data.find(c => c.id === payload.id);

        Object.assign(comment, payload);
    }
};
