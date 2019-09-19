<template>
    <drawer class="requests" :class="{empty: !loading.visible && !requests.data.length}" :visible.sync="visibleDrawer" @update:visible="resetDataFromDrawer" docked>
        <el-tabs type="border-card" v-model="activeTab" stretch v-if="openedRequest">
            <el-tab-pane name="chat" lazy>
                <div slot="label">
                    <i class="ti-comments"></i>
                    Chat
                </div>
                <chat ref="chat" :id="openedRequest.id" type="request" size="100%" max-size="100%" />
            </el-tab-pane>
            <el-tab-pane name="media" lazy>
                <div slot="label">
                    <i class="ti-gallery"></i>
                    Media
                </div>
                <div ref="media-content" id="media-content" class="content">
                    <media-gallery :media="openedRequest.media" :cols="2" :use-placeholder="!uploadedMedia.length" :gallery-options="{container: '#gallery'}" lazy-scroll-container="#media-content" lazy />
                    <el-divider>
                        <div v-if="uploadedMedia.length">
                            <el-button type="success" size="small" round :loading="uploadingMedia" @click="uploadMedia">
                                <template v-if="uploadingMedia">Uploading...</template>
                                <template v-else>Click here to upload {{uploadedMedia.length}} files</template>
                            </el-button>
                            <el-tooltip effect="dark" content="Reset upload" placement="bottom" v-if="!uploadingMedia">
                                <el-button type="danger" icon="el-icon-delete" size="small" :disabled="uploadingMedia" circle @click="$refs.upload.clear" />
                            </el-tooltip>
                        </div>
                        <template v-else>
                            <i class="el-icon-upload"></i> Upload files...
                        </template>
                    </el-divider>
                    <el-alert type="warning" title="Once confirmed the uploaded files, you can no longer delete them. Please proceed with caution!" :closable="false" center />
                    <el-divider />
                    <media-upload ref="upload" v-model="uploadedMedia" :loading="uploadingMedia" :size="mediaUploadMaxSize" :allowed-types="['image/jpg', 'image/jpeg', 'image/png', 'application/pdf']" :cols="2" :gallery-options="{container: '#gallery'}">
                        <template slot="trigger" slot-scope="scope">
                            <el-tooltip key="trigger" content="Drop files or click here to select" effect="dark" placement="bottom" >
                                <el-button class="trigger" icon="el-icon-plus" :style="scope.mediaItemStyle" @click="scope.triggerSelect" :disabled="uploadingMedia" />
                            </el-tooltip>
                        </template>
                    </media-upload>
                </div>
            </el-tab-pane>
            <el-tab-pane name="audit" lazy>
                <div slot="label">
                    <i class="ti-gallery"></i>
                    Audit
                </div>
                <audit :id="openedRequest.id" type="request" />
            </el-tab-pane>
        </el-tabs>
        <div slot="content" class="container" v-infinite-scroll="fetch">
            <placeholder :size="256" :src="require('img/5c7d3b0b0f0f4.png')" v-if="!loading.visible && !requests.data.length">
                <template v-if="hasFilters">
                    No requests found.
                    <small>Use the below button to reset the applied filters.</small>
                    <el-divider>
                        <el-button size="small" icon="el-icon-sort-up" round @click="resetFilters">Reset filters</el-button>
                    </el-divider>
                </template>
                <template v-else>
                    There are no requests available.
                    <small>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</small>
                </template>
            </placeholder>
            <div class="content" v-else-if="requests.data.length">
                <heading icon="icon-chat-empty" title="Requests">
                    <div slot="description" class="description">Need some info? Encountered an issue? Contact us!</div>
                    <el-button @click="addRequestDialogVisible = true" icon="ti-plus" round size="small" type="primary">
                        Add request
                    </el-button>
                </heading>
                <el-row :gutter="16">
                    <el-col :span="16">
                        <dynamic-scroller ref="dynamic-scroller" :items="requests.data" :min-item-size="249" page-mode>
                            <template v-slot="{item, index, active}">
                                <dynamic-scroller-item :item="item" :active="active" :data-index="index">
                                    <request-card :data="item" :visible-media-limit="3" :media-options="{container: '#gallery'}" @show-more-media="toggleDrawer(item, 'media')" @tab-click="$refs['dynamic-scroller'].forceUpdate" >
                                        <template #tab-overview-after>
                                            <el-button icon="el-icon-right" size="mini" @click="toggleDrawer(item)" plain round>View</el-button>
                                        </template>
                                        <template #tab-media-after>
                                            <el-divider v-if="!item.media.length">
                                                <el-button icon="el-icon-upload" round @click="toggleDrawer(item, 'media')">Upload files...</el-button>
                                            </el-divider>
                                        </template>
                                    </request-card>
                                </dynamic-scroller-item>
                            </template>
                            <template #after>
                                <div ref="loader"></div>
                            </template>
                        </dynamic-scroller>
                    </el-col>
                    <el-col class="hidden-md-and-down" :span="8" v-sticky="{stickyTop: 16}">
                        <el-card>
                            <filters ref="filters" :data.sync="filters.data" :schema="filters.schema" @changed="filtersChanged"/>
                            <el-button type="primary" icon="el-icon-sort-up" @click="resetFilters">Reset filters</el-button>
                        </el-card>
                    </el-col>
                </el-row>
                <el-dialog ref="add-request-dialog" title="Add request" :visible.sync="addRequestDialogVisible" custom-class="add-request-dialog" append-to-body>
                    <request-add-form ref="request-add-form" />
                    <span slot="footer" class="dialog-footer">
                        <el-button icon="el-icon-close" @click="addRequestDialogVisible = false" round>Cancel</el-button>
                        <el-button type="primary" icon="el-icon-check" round @click="addRequest">Confirm</el-button>
                    </span>
                </el-dialog>
            </div>
        </div>
    </drawer>
</template>

<script>
    import {MEDIA_UPLOAD_MAX_SIZE} from '@/config'
    import Chat from 'components/Chat2'
    import Audit from 'components/Audit'
    import Avatar from 'components/Avatar'
    import Drawer from 'components/Drawer'
    import Filters from 'components/Filters'
    import Heading from 'components/Heading'
    import Placeholder from 'components/Placeholder'
    import MediaUpload from 'components/MediaUpload'
    import MediaGallery from 'components/MediaGalleryList'
    import RequestCard from 'components/tenant/RequestCard'
    import RequestAddForm from 'components/tenant/RequestAddForm'
    import PQueue from 'p-queue'
    import {format} from 'date-fns'
    import VueSticky from 'vue-sticky'

    export default {
        components: {
            Chat,
            Audit,
            Avatar,
            Drawer,
            Filters,
            Heading,
            Placeholder,
            MediaUpload,
            MediaGallery,
            RequestCard,
            RequestAddForm,
        },
        directives: {
            sticky: VueSticky
        },
        data () {
            return {
                activeTab: 'chat',
                mediaUploadMaxSize: MEDIA_UPLOAD_MAX_SIZE,
                uploadedMedia: [],
                requests: {
                    data: []
                },
                filters: {
                    schema: [{
                        type: 'el-select',
                        title: 'Status',
                        name: 'status',
                        props: {
                            placeholder: 'Select the status',
                            clearable: true
                        },
                        children: Object.entries(this.$store.getters['application/constants'].service_requests.status).map(([value, label]) => ({
                            type: 'el-option',
                            props: {
                                label,
                                value
                            }
                        }))
                    }, {
                        type: 'el-select',
                        title: 'Priority',
                        name: 'priority',
                        props: {
                            placeholder: 'Select the priority',
                            clearable: true,
                        },
                        children: Object.entries(this.$store.getters['application/constants'].service_requests.priority).map(([value, label]) => ({
                            type: 'el-option',
                            props: {
                                label,
                                value
                            }
                        }))
                    }, {
                        type: 'el-date-picker',
                        title: 'Created',
                        name: 'created',
                        props: {
                            placeholder: 'Choose the created date',
                            valueFormat: 'yyyy-MM-dd',
                            format: 'dd.MM.yyyy',
                            style: 'width: 100%'
                        }
                    }, {
                        type: 'el-date-picker',
                        title: 'Due date',
                        name: 'due_date',
                        props: {
                            placeholder: 'Choose the due date',
                            format: 'dd.MM.yyyy',
                            valueFormat: 'yyyy-MM-dd',
                            style: 'width: 100%'
                        }
                    }],
                    data: {
                        status: null,
                        priority: null,
                        created: null,
                        due_date: null
                    },
                },
                loading: {
                    visible: true
                },
                openedRequest: null,
                visibleDrawer: false,
                uploadingMedia: false,
                addRequestDialogVisible: false
            }
        },
        filters: {
            formatDate (date) {
                return format(date, 'DD.MM.YYYY hh:mma')
            }
        },
        methods: {
            async fetch(params = {}) {
                if (this.loading.visible && this.requests.data.length) {
                    return
                }

                const {
                    current_page,
                    last_page
                } = this.requests;

                if (current_page && last_page && current_page == last_page) {
                    return
                }

                let page = current_page || 0

                page++;

                let loadingOptions = {
                    target: this.$el
                }

                if (!this.requests.data.length) {
                    loadingOptions.text = 'Fetching the requests...'
                } else {
                    loadingOptions.target = this.$refs.loader
                    loadingOptions.background = 'transparent'
                }

                this.loading = this.$loading(loadingOptions)

                try {
                    const {data: {data, ...rest}} = await this.$store.dispatch('getRequests', {
                        page,
                        per_page: 25,
                        sortedBy: 'desc',
                        orderBy: 'created_at',
                        ...params
                    })

                    this.requests = {data: [...this.requests.data, ...data], ...rest}
                } catch (err) {
                    this.$notify.error({
                        title: 'Oops!',
                        message: err
                    })
                } finally {
                    this.loading.close()
                }
            },
            async resetFilters () {
                if (this.$refs.filters) {
                    this.$refs.filters.reset()
                } else {
                    this.requests = {
                        data: []
                    }

                    Object.keys(this.filters.data).forEach(property => this.filters.data[property] = null)

                    await this.fetch()
                }
            },
            uploadMedia () {
                this.$confirm('Are you sure you want to upload these files?', 'Confirm', {
                    roundButton: true
                }).then(async () => {
                    this.uploadingMedia = true

                    const queue = new PQueue({concurrency: 1})

                    this.uploadedMedia.forEach(({file}) => queue.add(async () => {
                        try {
                            const {data} = await this.$store.dispatch('uploadRequestMedia', {
                                id: this.openedRequest.id,
                                media: file.src
                            })

                            this.openedRequest.media.push(data)

                            if (this.openedRequest.media.length === 1) {
                                this.$refs['dynamic-scroller'].forceUpdate()
                            }

                            this.$refs.upload.removeFile(file)
                        } catch (error) {
                            this.$notify.error({title: 'Oops!', message: error, position: 'bottom-left'})
                        }
                    }))

                    await queue.onIdle()

                    this.uploadingMedia = false
                    this.$refs.upload.clear()
                }).catch(() => null)
            },
            async filtersChanged (filters) {
                this.requests = {
                    data: []
                }

                await this.fetch(filters)
            },
            addRequest () {
                this.$watch(() => this.$refs['request-add-form'].loading, state => {
                    this.$nextTick(async () => {
                        this.$refs['request-add-form'].$el.classList.remove('el-loading-parent--relative')

                        if (!state) {
                            this.addRequestDialogVisible = false

                            this.requests = {
                                data: []
                            }

                            await this.fetch()
                        }
                    })
                })

                this.$refs['request-add-form'].submit()
            },
            toggleDrawer (request, tab = 'chat') {
                this.openedRequest = request;

                this.activeTab = tab

                if (this.activeTab === 'media') {
                    this.$nextTick(() => this.$refs['media-content'].scrollTop = this.$refs['media-content'].scrollHeight)
                }

                this.visibleDrawer = !this.visibleDrawer
            },
            resetDataFromDrawer () {
                this.activeTab = 'chat'
                this.uploadedMedia = []
                this.openedRequest = null
            }
        },
        computed: {
            hasFilters () {
                return Object.values(this.filters.data).some(value => value)
            }
        }
    }
</script>

<style lang="scss">
    .el-dialog.add-request-dialog {
        position: relative;
        z-index: 1;
        border-radius: 6px;
        max-width: 768px;
        overflow: hidden;
        .el-dialog__body {
            &:before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                z-index: -1;
                width: 100%;
                height: 100%;
                border-radius: 6px;
                background-image: url('~img/5c8eb142577df.png');
                background-repeat: no-repeat;
                background-size: 64em;
                background-position: -16em -7em;
                opacity: .16;
                pointer-events: none;
            }

        }
    }
</style>

<style lang="scss" scoped>
    .requests {
        height: auto !important;

        &:not(.empty):before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5c7d3b0b0f0f4.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .16;
            pointer-events: none;
        }
        .drawer {
            .el-tabs {
                display: flex;
                flex-direction: column;
                height: 100%;
                :global(.el-tabs__header) {
                    margin: 0;
                }
                :global(.el-tabs__content) {
                    padding: 0;
                    &, :global(.el-tab-pane) {
                        height: 100%;;
                        display: flex;
                        flex-direction: column;
                    }
                    #pane-media {
                        display: flex;
                        flex-direction: column;
                        height: 100%;
                        .content {
                            height: 100%;
                            overflow: auto;
                            padding: 8px;
                            display: flex;
                            flex-direction: column;
                            .media-upload {
                                .trigger {
                                    border-style: dashed;
                                    order: 1;
                                    position: relative;
                                    :global(i) {
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        width: 100%;
                                        height: 100%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        pointer-events: none;
                                    }
                                }
                            }
                            .el-alert {
                                overflow: visible;
                                margin-bottom: -12px;
                                :global(.el-alert__content) {
                                    text-align: center;
                                }
                            }
                            .el-divider {
                                flex-shrink: 0;
                                &.before {
                                    margin-bottom: 16px;
                                }
                                &.after {
                                    margin: 12px 0;
                                }
                                div {
                                    display: flex;
                                    align-items: center;
                                    i {
                                        font-size: 24px;
                                        margin-right: 5px;
                                    }
                                }
                            }
                        }
                    }
                    #pane-audit {
                        .audit :global(.el-timeline) {
                            padding: 24px;
                        }
                    }
                }
            }
        }
        .container {
            height: 100%;
            overflow-y: auto;
            position: relative;
            will-change: transform;
            -webkit-overflow-scrolling: touch;
            > .placeholder {
                height: 100% !important;
                font-size: 20px;
                color: darken(#F2F4F9, 32%);
                .el-divider :global(.el-divider__text) {
                    background-color: #F2F4F9;
                }
                small {
                    font-size: 72%;
                    color: darken(#F2F4F9, 16%);
                }
            }
            .content {
                padding: 16px;

                .heading {
                    margin-bottom: 24px;

                    .description {
                        color: darken(#fff, 40%);
                    }
                }

                .el-row {
                    .el-col {
                        &:first-child {
                            max-width: 640px;
                            .vue-recycle-scroller {
                                margin: -.5em -1em;
                                :global(.vue-recycle-scroller__item-wrapper) {
                                    :global(.vue-recycle-scroller__item-view) > div {
                                        padding: .5em 1em;
                                        .request-card {
                                            .el-button {
                                                float: right;
                                            }
                                        }
                                    }
                                }
                                :global(.vue-recycle-scroller__slot) .el-loading-parent--relative {
                                    min-height: 42px;
                                }
                            }
                        }
                        &:last-child {
                            max-width: 480px;
                            .el-card {
                                :global(.el-card__body) {
                                    padding: 16px;
                                    .el-button {
                                        width: 100%;
                                        margin-top: 16px;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
