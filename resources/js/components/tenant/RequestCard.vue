<template>
    <el-tabs class="request-card" stretch type="border-card" v-model="idState.activeTab" v-on="$listeners">
        <el-tab-pane name="overview">
            <slot name="tab-overview-before" />
            <div slot="label">
                <i class="el-icon-tickets"></i>
                Overview
            </div>
            <div class="title">
                <small>Category: {{data.category.name}}</small>
                {{data.title}}
            </div>
            <el-divider/>
            <div class="tags">
                Status:
                <el-tag :type="getTagTypeByStatus(data.status)" disable-transitions effect="plain" v-once>
                    {{getStatusName(data.status)}}
                </el-tag>
                <el-divider direction="vertical" />
                Priority:
                <el-tag :type="getTagTypeByPriority(data.priority)" disable-transitions effect="plain" v-once>
                    {{getPriorityName(data.priority)}}
                </el-tag>
                <template v-if="canShowQualification(data.qualification)">
                    <el-divider direction="vertical" />
                    Qualification:
                    <el-tag disable-transitions effect="plain" type="info" v-once>
                        {{getQualificationName(data.qualification)}}
                    </el-tag>
                </template>
            </div>
            <el-divider/>
            <read-more class="description" :text="data.description" :max-chars="512" more-str="Read more" less-str="Read less" />
            <el-divider v-if="assignees.length" />
            <div class="assignees" v-if="assignees.length">
                <div class="heading">Assignees</div>
                <div :key="assignee.id" class="assignee" v-for="assignee in visibleAssignees">
                    <avatar :name="assignee.name" :size="32" :src="assignee.avatar" />
                    <div>
                        {{assignee.name}}
                        <small>{{assignee.email}}</small>
                    </div>
                </div>
                <div class="more" v-if="!idState.showAllAssginees && assignees.length > 4">
                    <avatar :key="assignee.id" :name="assignee.name" :size="32" :src="assignee.avatar" v-for="assignee in assignees.slice(3)" />
                    <el-link @click="showRestAssignees" type="success">and {{assignees.slice(3).length}} more</el-link>
                </div>
            </div>
            <el-divider/>
            <div class="user">
                <avatar :name="data.tenant.user.name" :size="32" :src="data.tenant.user.avatar" />
                <div>
                    {{data.tenant.user.name}}
                    <small>
                        created on {{data.created_at | formatDate}}
                        <template v-if="isRequestFinished(data)">
                            and solved on {{data.solved_date | formatDate}}
                        </template>
                    </small>
                </div>
            </div>
            <slot name="tab-overview-after" />
        </el-tab-pane>
        <el-tab-pane name="media">
            <div slot="label">
                <i class="el-icon-picture-outline"></i>
                Media
            </div>
            <slot name="tab-media-before" />
            <media-gallery :limit="visibleMediaLimit" :options="mediaOptions" :media="data.media">
                <el-button key="upload" slot-scope="scope" :style="scope.itemStyle" @click="$emit('show-more-media')" v-if="visibleMediaLimit">
                    <i class="el-icon-more"></i>
                    View all
                </el-button>
            </media-gallery>
            <slot name="tab-media-after" />
        </el-tab-pane>
    </el-tabs>
</template>

<script>
    import Avatar from 'components/Avatar'
    import MediaGallery from 'components/MediaGalleryList'
    import {format} from 'date-fns'
    import {IdState} from 'vue-virtual-scroller'

    export default {
        mixins: [
            IdState({
                idProp: vm => vm.data.id
            })
        ],
        props: {
            data: {
                type: Object,
                default: {
                    media: []
                },
            },
            visibleMediaLimit: {
                type: Number,
                default: 0
            },
            mediaOptions: {
                type: Object,
                default: () => ({})
            },
            mediaLazyScrollContainer: {}
        },
        filters: {
            formatDate (date) {
                return format(date, 'DD.MM.YYYY hh:mma')
            }
        },
        components: {
            Avatar,
            MediaGallery
        },
        idState () {
            return {
                activeTab: 'overview',
                showAllAssginees: false
            }
        },
        methods: {
            getStatusName (status) {
                return this.$t(`models.request.status.${this.$constants.service_requests.status[status]}`)
            },
            getTagTypeByStatus (status) {
                let type;

                switch (this.getStatusName(status)) {
                    case 'received':
                    case 'archived':
                        type = 'info';

                        break;
                    case 'in_processing':
                    case 'assigned':
                    case 'reactivated':
                        type = 'warning';

                        break;
                    case 'done':
                        type = 'success'
                }

                return type
            },
            getPriorityName (priority) {
                return this.$t(`models.request.priority.${this.$constants.service_requests.priority[priority]}`)
            },
            getTagTypeByPriority (priority) {
                let type

                switch (this.getPriorityName(priority)) {
                    case 'low':
                        type = 'info';

                        break;
                    case 'normal':
                        type = 'warning';

                        break;
                    case 'urgent':
                        type = 'danger'
                }

                return type
            },
            getQualificationName (qualification) {
                return this.$t(`models.request.qualification.${this.$constants.service_requests.qualification[qualification]}`)
            },
            isRequestFinished (request) {
                return this.$constants.service_requests.status[request.status] === 'done'
            },
            canShowQualification (qualification) {
                return this.$constants.service_requests.qualification[qualification] !== 'none'
            },
            showRestAssignees () {
                this.idState.showAllAssginees = true;

                this.$emit('tab-click')
            }
        },
        computed: {
            assignees () {
                return [...this.data.assignees, ...this.data.providers]
            },
            visibleAssignees () {
                if (this.idState.showAllAssginees) {
                    return this.assignees
                }

                if (this.assignees.length === 4) {
                    return this.assignees.slice(0, 4)
                }

                return this.assignees.slice(0, 3)
            }
        }
    };
</script>

<style lang="scss" scoped>
    .el-tabs.request-card {
        color: lighten(#000, 32%);
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76) !important;

        :global(.el-tabs__header) {
            :global(.el-tabs__item):last-child {
                border-right-style: none;
            }
        }

        :global(.el-tabs__content) {
            padding: 16px;

            #pane-overview {
                .title {
                    font-size: 16px;
                    text-overflow: ellipsis;
                    overflow: hidden;
                    white-space: nowrap;
                    line-height: 1.8;

                    small {
                        font-size: 80%;
                        display: block;
                        color: darken(#fff, 40%);
                    }
                }

                .tags {
                    display: flex;
                    align-items: center;
                    padding: 12px 0;
                    background: linear-gradient(to right, transparent, darken(#fff, 2%), transparent);
                    color: darken(#fff, 64%);
                    margin: -12px 0;

                    .el-tag {
                        height: auto;
                        font-weight: bold;
                        line-height: 1.48;
                        border-radius: 12px;
                        text-transform: uppercase;
                        margin-left: 4px;
                    }
                }

                .description {
                    color: darken(#fff, 40%);
                    :global(p) {
                        margin: 0;
                        white-space: pre-wrap;
                    }
                    :global(a) {
                        color: #6AC06F;
                        text-decoration: none;
                        transition: color .48s;
                        &:focus,
                        &:hover {
                            color: lighten(#6AC06F, 16%);
                        }
                    }
                }

                .assignees {
                    .heading {
                        margin-bottom: 12px;
                    }

                    .assignee {
                        display: flex;
                        align-items: center;

                        div:last-child {
                            line-height: 1.24;
                            font-weight: 500;
                            margin-left: 8px;

                            small {
                                font-size: 96%;
                                display: block;
                                color: darken(#fff, 40%);
                            }
                        }

                        &:not(:last-child) {
                            margin-bottom: 12px;
                        }
                    }

                    .more {
                        .avatar {
                            &:not(:first-child) {
                                margin-left: -10px;
                                border: 2px #fff solid;
                            }
                        }
                    }
                }

                .user {
                    display: inline-flex;
                    align-items: center;

                    div:last-child {
                        line-height: 1.24;
                        font-weight: 500;
                        margin-left: 8px;

                        small {
                            font-size: 96%;
                            display: block;
                            color: darken(#fff, 40%);
                        }
                    }
                }

                > .el-divider {
                    margin: 12px 0;
                    background: linear-gradient(to right, transparent, darken(#fff, 8%), transparent);
                }

                .el-button {
                    float: right;
                }
            }

            #pane-media {
                .media-gallery .el-button {
                    position: relative;
                    border-width: 2px;
                    border-style: dashed;
                    :global(span) {
                        width: 100%;
                        height: 100%;
                        position: absolute;
                        top: 0;
                        left: 0;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        i {
                            font-size: 32px;
                        }
                    }
                }
            }
        }
    }
</style>
