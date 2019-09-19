<template>
    <div class="action-group">
        <el-button @click="SaveAndEdit" size="small" type="primary" round> {{this.$t('actions.save')}}</el-button>
        <el-button @click="saveAndClose" size="small" type="primary" round> {{this.$t('actions.saveAndClose')}}
        </el-button>
        <el-button v-if="deleteAction || undefined"  @click="deleteAndClose" size="small" type="danger" round icon="ti-trash"> {{this.$t('actions.delete')}}</el-button>
        <el-button @click="goToListing" size="small" type="warning" round> {{this.$t('actions.close')}}
        </el-button>
    </div>
</template>

<script>
    import {displayError, displaySuccess} from "helpers/messages";

    export default {
        props: {
            saveAction: {
                type: Function,
                required: true
            },
            deleteAction: {
                type: Function,
                required: false
            },
            route: {
                type: String,
                required: true
            },
            editRoute: {
                type: String,
                required: false
            },
            queryParams: { 
                type: Object,
                default() {
                    return {}
                }
            }
        },
        methods: {
            goToListing() {
                return this.$router.push({
                    name: this.route,
                    query: this.queryParams
                })
            },
            async saveAndClose() {
                try {
                    const resp = await this.saveAction();
                    if (resp) {
                        this.goToListing();
                    }
                } catch (e) {
                    console.log(e)
                }
            },
            deleteAndClose() {
                this.$confirm('This action is irreversible. Please proceed with caution.', 'Are you sure?', {
                        type: 'warning'
                    }).then(() => {
                        this.callDeleteAction();
                    }).catch(() => {
                    });
            },
            async SaveAndEdit() {
                try {
                    const resp = await this.saveAction();
                    if (resp && resp.data) {
                        this.$router.push({
                            name: this.editRoute,
                            params: {id: resp.data.id}
                        })
                    }
                } catch (e) {
                    console.log(e)
                }

            },

            async callDeleteAction() {
                const resp = await this.deleteAction({id: parseInt(this.$route.params.id)})
                    .then(r => {
                        displaySuccess(r);
                        this.goToListing();
                    })
                    .catch(err => displayError(err)); 
            }
        }
    }
</script>

<style scoped>
    .action-group > .el-button:not(:first-child) {
        margin-left: 0px;
    }
</style>
