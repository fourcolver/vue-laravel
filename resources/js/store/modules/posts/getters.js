export default {
    posts({posts: {data = []}}, getters, rootState) {
        const {application: {constants: {posts: postConstants}}} = rootState;

        return data.map(post => {
            post.status_label = postConstants.status[post.status];
            post.visibility_label = postConstants.visibility[post.visibility];
            post.type_label = postConstants.type[post.type];
            post.preview = `${post.content.substring(0, 50)}...`;
            return post;
        });
    },
    postsMeta({posts}) {
        return _.omit(posts, 'data');
    }
}
