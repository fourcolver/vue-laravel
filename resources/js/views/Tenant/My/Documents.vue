<template>
    <placeholder :size="256" :src="require('img/5c98a90bb5c05.png')" v-if="!this.loader.visible && isEmpty">
        There are no documents available.
        <small>All of them will be listed here in columns by category.</small>
    </placeholder>
    <div class="documents" v-else-if="!isEmpty">
        <el-card v-sticky="{stickyTop: -16}">
            <heading icon="ti-book" title="My documents">
                <div slot="description">A list with all the building and unit's documents.</div>
            </heading>
        </el-card>
        <el-divider />
        <el-row>
            <el-col :span="24" v-for="(files, category) in documents" :key="category">
                <div class="title">
                    {{$t(`models.building.${category}`)}}
                    <small>{{files.length}} documents available</small>
                </div>
                <gallery-list :media="files" :cols="4" />
                <el-divider />
            </el-col>
        </el-row>
    </div>
</template>

<script>
    import Heading from 'components/Heading';
    import {displayError} from "helpers/messages";
    import Placeholder from 'components/Placeholder'
    import GalleryList from 'components/MediaGalleryList'
    import VueSticky from 'vue-sticky'

    export default {
        components: {
            Heading,
            Placeholder,
            GalleryList,
        },
        directives: {
            sticky: VueSticky
        },
        data() {
            return {
                documents: {},
                loader: {
                    visible: true
                },
                imageIdx: null
            }
        },
        computed: {
            isEmpty () {
                return !Object.keys(this.documents).length
            }
        },
        async mounted () {
            this.loader = this.$loading({
                target: this.$el.parentElement,
                text: 'Fetching the documents...'
            })

            try {
                const {media} = await this.$store.dispatch('getBuilding', {id: this.$store.getters.loggedInUser.tenant.building_id})

                this.documents = media.reduce((obj, file) => {
                    obj[file.collection_name] = obj[file.collection_name] || []
                    obj[file.collection_name].push(file)

                    return obj
                }, {})
            } catch (err) {
                displayError(err)
            } finally {
                this.loader.close()
            }
        }
    }
</script>

<style lang="scss" scoped>
   .placeholder {
        height: 100% !important;
        font-size: 20px;
        color: darken(#F2F4F9, 32%);
        small {
            font-size: 72%;
            color: darken(#F2F4F9, 16%);
        }
    }
    .documents {

        &:before {
            content: '';
            position: fixed;
            bottom: 0;
            right: 0;
            background-image: url('~img/5ceaf5545afd8.png');
            background-repeat: no-repeat;
            background-position: 100% 100%;
            width: 100%;
            height: 100%;
            opacity: .08;
            pointer-events: none;
        }

        .el-card {
            max-width: 1024px;
            :global(.el-card__body) {
                padding: 12px 16px;
                .heading div {
                    color: darken(#fff, 40%);
                }
            }
        }

        .el-row {
            max-width: 1024px;
            .el-col {
                .title {
                    font-size: 18px;
                    margin-bottom: 24px;
                    small {
                        display: block;
                        color: darken(#fff, 40%);
                    }
                }
                &:last-child .el-divider {
                    display: none;
                }
            }
        }
    }
</style>
