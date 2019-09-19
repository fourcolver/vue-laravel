export default {
    constants({ constants }) {
        return constants;
    },
    requests({ constants: { service_requests } }) {
        return service_requests;
    },
    tenants({ constants: { tenants } }) {
        return tenants;
    },
    posts({ constants: { posts } }) {
        return posts;
    },
    getAudit: state => (id, type) => id ? state.audits[type][id] : state.audits[type]
}