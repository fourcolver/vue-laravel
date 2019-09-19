import {mapActions, mapGetters} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';
import ListTable from 'components/ListTable';

export default ({
                    header,
                    actions: {
                        delete: deleteAction,
                        get: getAction,
                        getParams
                    },
                    getters: {
                        items,
                        pagination
                    }
                }) => ({
    components: {
        ListTable
    },
    data() {
        return {
            items: [],
            header: [],
            loading: false,
            filtersHeader: '',
            selectedItems: []
        };
    },
    methods: {
        ...mapActions([getAction, deleteAction]),

        async fetchMore(params = {
            ...this.$route.query,
            ...this.fetchMoreParams
        }) {
            try {
                this.loading = true;

                if (getParams) {
                    params = {...params, ...getParams};
                }

                await this[getAction](params)
            } catch (err) {
                displayError(err);
            } finally {
                this.loading = false;
            }
        },
        selectionChanged(rows) {
            this.selectedItems = rows;
        },
        batchDelete() {
            this.$confirm('This action is irreversible. Please proceed with caution.', 'Are you sure?', {
                type: 'warning'
            }).then(() => {
                Promise.all(this.selectedItems.map((item) => {
                    return this[deleteAction](item)
                        .then(r => {
                            displaySuccess(r);
                        })
                        .catch(err => displayError(err));
                })).then(() => {
                    this.fetchMore();
                })
            }).catch(() => {
            });
        }
    },
    computed: {
        ...mapGetters([items, pagination]),
        filters() {
            return [];
        },
        total() {
            return parseInt(this[pagination].total);
        },
        currSize() {
            return parseInt(this[pagination].per_page);
        },
        currPage() {
            return parseInt(this[pagination].current_page);
        }
    },
    watch: {
        [items](newValue) {
            this.items = newValue;
        }
    }
});
