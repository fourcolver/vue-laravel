import {mapActions, mapGetters} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';

export default class MixinBuilder {

    static capitalize(name) {
        return name.charAt(0).toUpperCase() + name.slice(1);
    }

    static getStoreActionNames(singular, plural) {
        return [
            `fetch${this.capitalize(plural)}`,
            `delete${this.capitalize(singular)}`
        ]
    }

    static getStoreGetterNames(singular, plural) {
        const pluralCap = this.capitalize(plural);
        return [`get${pluralCap}`, `get${pluralCap}Meta`];
    }

    static getAddMethodName(singular) {
        return `add${this.capitalize(singular)}`;
    }

    static getListingMixin(singularModelName, pluralModelName, getParams = {}) {
        const actions = MixinBuilder.getStoreActionNames(singularModelName, pluralModelName);
        const getters = MixinBuilder.getStoreGetterNames(singularModelName, pluralModelName);
        const [fetchModelAction, deleteModelAction] = actions;
        const [modelCollectionGetter, modelMetaGetter] = getters;

        return {
            data () {
                return {
                    loading: false
                }
            },
            methods: {
                ...mapActions(actions),
                [MixinBuilder.getAddMethodName(singularModelName)]() {
                    this.$router.push({
                        name: `admin${MixinBuilder.capitalize(pluralModelName)}Add`
                    });
                },
                getMore(params) {
                    const requestParams = {
                        ...params
                    };

                    if (getParams.role) {
                        requestParams.role = this.$route.query.role;
                    }

                    this.loading = true;
                    this[fetchModelAction](requestParams)
                        .catch(err => displayError(err))
                        .finally(() => this.loading = false);
                },
                edit(data) {
                    this.$router.push({
                        name: `admin${MixinBuilder.capitalize(pluralModelName)}Edit`,
                        params: {
                            id: data.id
                        }
                    });
                },
                remove(model) {
                    this.$confirm(this.$t(`models.${singularModelName}.confirmDelete.title`), this.$t(`models.${singularModelName}.confirmDelete.text`), {
                        type: 'warning'
                    }).then(() => {
                        this[deleteModelAction](model)
                            .then(r => displaySuccess(r))
                            .catch(err => displayError(err));
                    }).catch(() => {
                    });
                }
            },
            computed: {
                ...mapGetters(getters),
                total() {
                    return this[modelMetaGetter].total;
                },
                currPage() {
                    return this[modelMetaGetter].current_page;
                },
                currSize() {
                    return parseInt(this[modelMetaGetter].per_page);
                }
            }
        }
    }
}
