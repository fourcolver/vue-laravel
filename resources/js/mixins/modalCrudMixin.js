import {mapActions} from 'vuex';
import {displayError, displaySuccess} from "helpers/messages";

export default (config) => {
    const {actions} = config;

    return {
        data() {
            return {
                showModal: false,
                modeEdit: false,
                isReady: false,
                fetchParams: {},
            }
        },
        computed: {
            modalText() {
                let mode = 'add';

                if (this.modeEdit) {
                    mode = 'edit';
                }

                return {
                    title: this.$t(`models.${this.i18nName}.${mode}`),
                    button: this.$t(`models.${this.i18nName}.${mode}`),
                }
            }
        },
        methods: {
            ...mapActions([actions.get, actions.delete, actions.update, actions.create]),
            openAdd() {
                this.modeEdit = false;
                this.showModal = true;
            },
            async openEdit({id}) {
                this.modeEdit = true;
                this.model = await this[actions.get]({id});
                this.showModal = true;
            },
            remove(row) {
                this.$confirm('This action is irreversible. Please proceed with caution.', 'Are you sure?', {
                    type: 'warning'
                }).then(() => {
                    this[actions.delete](row)
                        .then(r => {
                            this.fetchMore();

                            displaySuccess(r);
                        })
                        .catch(err => displayError(err));
                }).catch(() => {
                });
            },
            async submitForm() {
                try {
                    const valid = await this.$refs.form.validate();
                    if (valid) {
                        let data;

                        if (this.modeEdit) {
                            data = await this[actions.update](this.model);
                        } else {
                            data = await this[actions.create](this.model);
                        }

                        await this.fetchMore();
                        this.showModal = false;
                        displaySuccess(data);
                    }
                } catch (e) {
                    displayError(e);
                }
            }
        },
        created() {
            this.isReady = true;
        },
        watch: {
            showModal(newVal) {
                if (!newVal) {
                    this.model = {};
                    this.$refs.form.resetFields();
                }
            }
        }
    }

}