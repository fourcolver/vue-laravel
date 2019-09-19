export default {
    templates({templates}) {
        return templates.data;
    },
    templatesMeta({templates}) {
        return _.omit(templates, 'data');
    }
}
