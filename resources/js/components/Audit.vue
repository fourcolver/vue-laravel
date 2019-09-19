<template>
    <div class="audit">
        <placeholder :src="require('img/5ce8f4e279cb2.png')" v-if="isEmpty">
            No activity available for now!
            <small>All available activities will appear here in chronological order.</small>
        </placeholder>
        <el-timeline v-infinite-scroll="fetch" v-else>
            <el-timeline-item v-for="(audit, date) in audits.data" :key="audit.id" :timestamp="date | formatDate">
                {{audit.content}}
            </el-timeline-item>
            <el-timeline-item v-if="loading">
                Loading...
            </el-timeline-item>
        </el-timeline>
    </div>
</template>

<script>
    import Placeholder from 'components/Placeholder'
    import {format} from 'date-fns'

    export default {
        props: {
            id: {
                type: Number
            },
            type: {
                type: String,
                required: true,
                validator: type => ['post', 'product', 'request'].includes(type)
            }
        },
        components: {
            Placeholder
        },
        data () {
            return {
                audits: {
                    data: {}
                },
                categories: [],
                loading: true
            }
        },
        filters: {
            formatDate (date) {
                return format(date, 'DD.MM.YYYY hh:mma')
            }
        },
        methods: {
            async fetch (params) {
                const {
                    current_page,
                    last_page
                } = this.audits

                if (current_page && last_page &&
                    current_page == last_page) {
                    return
                }

                this.loading = true

                let page = current_page || 0

                page++

                try {
                    await this.$store.dispatch('application/fetchAudits', {
                        sortedBy: 'desc',
                        orderBy: 'created_at',
                        page,
                        per_page: 25,
                        auditable_id: this.id,
                        auditable_type: this.type,
                        ...params
                    })

                    const {data, ...rest} = this.$store.getters['application/getAudit'](this.id, this.type)
            
                    switch (this.type) {
                        case 'post':
                            // TBD

                            break
                        case 'product':
                            // TBD

                            break
                        case 'request':
                            const {
                                status,
                                priority,
                                qualification
                            } = this.$constants.service_requests

                            const audits = data.reduce((obj, {
                                id,
                                user,
                                event,
                                user_id,
                                created_at,
                                new_values: {
                                    user_name,
                                    provider_name,
                                    title: newTitle,
                                    status: newStatus,
                                    due_date: newDueDate,
                                    priority: newPriority,
                                    category_id: newCategory,
                                    qualification: newQualification
                                },
                                old_values: {
                                    title: oldTitle,
                                    status: oldStatus,
                                    due_date: oldDueDate,
                                    priority: oldPriority,
                                    category_id: oldCategory,
                                    qualification: oldQualification
                                },
                                auditable_id,
                                auditable_type
                            }, idx) => {
                                let content

                                switch (event) {
                                    case 'created':
                                        content = `${user.name} opened this ${auditable_type}.`

                                        if (!this.id) {
                                            content = `${user.name} opened ${auditable_type} #${auditable_id}.`
                                        }

                                        obj[created_at] = {id, event, content}

                                        break
                                    case 'updated':
                                        if (newTitle && oldTitle) {
                                            content = `The title changed from ${oldTitle} to ${newTitle}.`

                                            if (!this.id) {
                                                content = `The title changed from ${oldTitle} to ${newTitle} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        if (newStatus && oldStatus) {
                                            oldStatus = status[oldStatus]
                                            newStatus = status[newStatus]

                                            content = `The status changed from ${oldStatus} to ${newStatus}.`

                                            if (!this.id) {
                                                content = `The status changed from ${oldStatus} to ${newStatus} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        if (newDueDate && oldDueDate) {
                                            oldDueDate = format(oldDueDate, 'dddd DD, MMMM YYYY')
                                            newDueDate = format(newDueDate, 'dddd DD, MMMM YYYY')

                                            content = `The due date changed from ${oldDueDate} to ${newDueDate}.`

                                            if (!this.id) {
                                                content = `The due date changed from ${oldDueDate} to ${newDueDate} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        if (newPriority && oldPriority) {
                                            oldPriority = priority[oldPriority]
                                            newPriority = priority[newPriority]

                                            content = `The priority changed from ${oldPriority} to ${newPriority}.`

                                            if (!this.id) {
                                                content = `The priority changed from ${oldPriority} to ${newPriority} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        if (newCategory && oldCategory) {
                                            oldCategory = this.categories[oldCategory]
                                            newCategory = this.categories[newCategory]

                                            content = `The category changed from ${oldCategory} to ${newCategory}.`

                                            if (!this.id) {
                                                content = `The category changed from ${oldCategory} to ${newCategory} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        if (newQualification && oldQualification) {
                                            oldQualification = this.$t(`models.request.qualification.${qualification[oldQualification]}`).toLowerCase()
                                            newQualification = this.$t(`models.request.qualification.${qualification[newQualification]}`).toLowerCase()

                                            content = `The qualification changed from ${oldQualification} to ${newQualification}.`

                                            if (!this.id) {
                                                content = `The qualification changed from ${oldQualification} to ${newQualification} on ${auditable_type} #${auditable_id}.`
                                            }

                                            obj[created_at] = {id, event, content}
                                        }

                                        break
                                    case 'provider_assigned':
                                        content = `${provider_name} has been assigned as provider.`

                                        if (!this.id) {
                                            content = `${provider_name} has been assigned as provider on ${auditable_type} #${auditable_id}.`
                                        }

                                        obj[created_at] = {id, event, content}

                                        break
                                    case 'user_assigned':
                                        content = `${user_name} has been assigned as manager.`

                                        if (!this.id) {
                                            content = `${user_name} has been assigned as manager on ${auditable_type} #${auditable_id}.`
                                        }

                                        obj[created_at] = {id, event, content}

                                        break
                                    case 'media_uploaded':
                                        content = `Media uploaded.`

                                        if (!this.id) {
                                            content = `Media uploaded on ${auditable_type} #${auditable_id}.`
                                        }

                                        obj[created_at] = {id, event, content}
                                        
                                        break
                                    case 'media_deleted':
                                        content = `Media deleted.`

                                        if (!this.id) {
                                            content = `Media deleted on ${auditable_type} #${auditable_id}.`
                                        }

                                        obj[created_at] = {id, event, content}

                                    break
                                }

                                return obj
                            }, {})

                            this.audits = {data: {...this.audits.data, ...audits}, ...rest}

                            break
                    }
                } catch (err) {
                    this.$message.error(err, {
                        offset: 88
                    })
                } finally {
                    this.loading = false
                }
            }
        },
        computed: {
            isEmpty () {
                return !this.loading && !Object.keys(this.audits.data).length
            }
        },
        async mounted () {
            let {data} = await this.$store.dispatch('getRequestCategoriesTree', {get_all: true})

            const flattenCategories = categories => categories.reduce((obj, category) => {
                obj[category.id] = category.name.toLowerCase()

                if (category.categories) {
                    obj = {...obj, ...flattenCategories(category.categories)}

                    delete category.categories;
                }

                return obj
            }, {})

            this.categories = flattenCategories(data)
        }
    }
</script>

<style lang="scss" scoped>
    .audit {
        height: 100%;
        display: flex;
        flex-direction: column;
        .placeholder {
            font-size: 16px;
            small {
                color: darken(#fff, 28%);
            }
        }
        .el-timeline {
            padding: 0;
            height: 100%;
            overflow: auto;
        }
    }
</style>