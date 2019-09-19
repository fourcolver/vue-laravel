<template>
    <div class="news">
        <heading icon="icon-megaphone-1" title="News">
            <div slot="description" class="description">Sed placerat volutpat mollis.</div>
        </heading>
        <el-row :gutter="24">
            <el-col :span="16">
                <announcement class="notice" v-model="model.content" :loading="isPublishing" @onEnter="handlePublishing">
                    <ul class="extra" slot="extra">
                        <li>
                            Visibility
                            <el-select placeholder="Select visibility" size="mini" v-model="model.visibility">
                                <el-option :key="key" :label="type" :value="key" v-for="(type, key) in visibilityTypes" />
                            </el-select>
                        </li>
                        <li>
                            <media-upload ref="upload" :cols="6" :allowed-types="['image/jpeg', 'image/png', 'application/pdf']" v-model="model.files">
                                <template slot="trigger" slot-scope="scope">
                                    <el-tooltip content="Drop files or click here to select" effect="dark" key="trigger" placement="bottom">
                                        <el-button class="uploader-trigger" icon="el-icon-plus" :style="scope.mediaItemStyle" @click="scope.triggerSelect" />
                                    </el-tooltip>
                                </template>
                            </media-upload>
                        </li>
                    </ul>
                </announcement>
                <loader :size="24" :visible="loading" centered/>
                <post v-for="post in posts.data" :key="post.id" :data="post" />
                <!-- <post :key="post.id" :post="post" class="post" v-for="post in posts.data"/> -->
            </el-col>
            <el-col :span="8" v-sticky="{stickyTop: 56}">
                <el-card>
                    <div class="title" slot="header">Filters</div>
                    <filters :data="filters.data" :schema="filters.schema" @changed="filtersChanged"/>
                </el-card>
                <rss-feed title="Blick.ch News"/>
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import Announcement from 'components/tenant/PostAdd';
    import Post from 'components/tenant/Post'
    import {mapGetters, mapActions, mapState} from 'vuex';
    import {displaySuccess, displayError} from 'helpers/messages';
    import postTypes from 'mixins/methods/postTypes';
    import UploadMixin from 'mixins/uploadMixin';
    import Loader from 'components/SimpleLoader';
    import RssFeed from 'components/tenant/RSSFeed';
    import Filters from 'components/Filters';
    import Card from 'components/Card';
    import Sticky from 'vue-sticky-directive'
    import MediaUpload from 'components/MediaUpload'
    import VueSticky from 'vue-sticky'

    export default {
        name: 'TenantNews',
        mixins: [UploadMixin],
        directives: {
            Sticky
        },
        components: {
            Heading,
            Post,
            Announcement,
            Loader,
            RssFeed,
            Filters,
            Card,
            MediaUpload,
        },
        directives: {
            sticky: VueSticky
        },
        data() {
            const filterSchema = [{
                type: 'el-select',
                title: 'Category',
                name: 'category',
                props: {
                    clearable: true
                },
                children: [{
                    type: 'el-option',
                    props: {
                        label: 'All',
                        value: null
                    }
                }, {
                    type: 'el-option',
                    props: {
                        label: 'My posts',
                        value: 1
                    }
                }, {
                    type: 'el-option',
                    props: {
                        label: 'From the neighbourhood',
                        value: 2
                    }
                }, {
                    type: 'el-option',
                    props: {
                        label: 'From the district',
                        value: 3
                    }
                }]
            }];

            const filterData = {
                category: null
            };

            return {
                model: {
                    visibility: '',
                    content: '',
                    files: []
                },
                isPublishing: false,
                text: '',
                posts: {
                    data: []
                },
                offset: {
                    bottom: false,
                    top: 148,
                    sticked: true
                },
                filters: {
                    schema: filterSchema,
                    data: filterData
                },
                stickyOffset: {
                    top: 89
                },
                loading: false,
                tenant: {}
            }
        },
        methods: {
            ...mapActions([
                'getPosts',
                'createPost',
                'uploadPostMedia',
                'myTenancy'
            ]),

            async filtersChanged(filters) {
                let {category, ...newFilters} = filters;

                const {id, tenant} = this.$store.getters.loggedInUser;

                if (category === 1) {
                    newFilters.user_id = id;
                }

                if (tenant) {
                    const {address_id, district_id} = tenant;

                    if (address_id && category === 2) {
                        newFilters.address_id = address_id;
                    }

                    if (district_id && category === 2) {
                        newFilters.district_id = district_id;
                    }
                }

                await this.get(newFilters);
            },
            async handlePublishing() {
                if (!this.model.content
                    || !this.model.visibility) {
                    return;
                }

                this.isPublishing = true;

                try {
                    const {files, ...params} = this.model;
                    const data = await this.createPost(params);

                    displaySuccess(data);

                    const {data: {id}} = data;

                    if (files.length) {
                        let promises = files.map(({file}) => this.uploadPostMedia({
                            id,
                            media: file.src
                        }));

                        await Promise.all(promises);
                    }

                    this.model.content = '';

                    this.$refs.upload.clear()
                } catch (err) {
                    displayError(err);
                } finally {
                    this.isPublishing = false;
                }
            },

            async get(params = {}) {
                const postStatuses = this.$constants.posts.status;
                const publishedStatus = Object.keys(postStatuses).find(k => postStatuses[k] === 'published');

                try {
                    this.posts.data = [];
                    this.loading = true;

                    const {data} = await this.$store.dispatch('posts2/get', {
                        per_page: 99999, // temporary
                        status: parseInt(publishedStatus, 10),
                        feed: 1,
                        ...params
                    });

                    this.posts = data;

                } catch (err) {
                    displayError(err);
                } finally {
                    this.loading = false;
                }
            }
        },
        computed: {
            visibilityTypes() {
                let types = postTypes.visibility;

                if (_.isEmpty(this.tenant) || _.isEmpty(this.tenant.building) || (!_.has(this.tenant.building, 'district_id') || this.tenant.building.district_id == null)) {
                    types = _.omit(postTypes.visibility, 2)
                }

                return types;
            }
        },
        async created() {
            this.myTenancy().then((resp) => {
                this.tenant = resp.data;
            }).catch((error) => {
                displayError(error);
            });

            this.model.visibility = Object.keys(this.visibilityTypes)[0];

            await this.get();
        }
    }
</script>

<style lang="scss" scoped>
    .scroller {
        /*height: 100%;*/
    }

    .news {
        &:before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/51177185_23843277688790167_2069589399565238272_n.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: calc(100% - 320px);
            height: calc(100% - 73px);
            opacity: .16;
        }

        .heading {
            margin-bottom: 24px;

            .description {
                color: darken(#fff, 40%);
            }
        }

        .el-row {
            .el-col {
                .el-card:not(:last-of-type) {
                    margin-bottom: 2em;
                }

                &:first-of-type {
                    max-width: 640px;

                    .uploader-trigger {
                        border-style: dashed;
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
                        }
                    }

                    .card {
                        &:not(:last-of-type) {
                            margin: 2em 0;
                        }
                    }
                }

                &:last-of-type {
                    max-width: 448px;
                }
            }
        }

        .notice {
            .extra {
                margin: -1em;
                padding: 0;
                list-style: none;

                li {
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    padding: .5em 1em;

                    &:nth-child(2) {
                        border-top: 1px darken(#fff, 8%) solid;

                    }
                }
            }
        }

        .notice,
        .post:not(:last-of-type) {
            margin-bottom: 2em;
        }
    }
</style>
