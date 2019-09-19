<template>
    <card :class="{pinned: data.pinned, 'new-neighbour': isNewNeighbourType}" class="post">
        <template v-if="isNewNeighbourType">
            <div class="content">
                <small>Hey, there's a new neighbour around.</small>
                Say hello to <b>{{tenant}}</b>!
            </div>
            <div class="likes" v-if="data.likes.length">
                <div class="users">
                    <template v-if="data.liked">You</template>
                    <template v-else>{{ data.likes[data.likes.length - 1].name }}</template>
                    <small>
                        <template v-if="data.likes_count > 1">
                            and {{data.likes_count - 1}} others greeted {{data.tenant.title === 'mrs' ? 'her':'him'}}
                        </template>
                        <template v-else>greeted {{data.tenant.title === 'mrs' ? 'her':'him'}}</template>
                    </small>
                </div>
            </div>
            <reactions :id="data.id" like-text="Greetings" unlike-text="Bye" type="posts" />
        </template>
        <template v-else>
            <div class="pinned" v-if="data.pinned"><span>pinned</span></div>
            <div class="user">
                <avatar :name="data.user.name" :size="44" :src="data.user.avatar" />
                <div class="name">
                    {{data.user.name}}
                    <small>
                        {{formatDatetime(data.created_at)}}
                    </small>
                </div>
            </div>
            <div class="title" v-if="data.pinned">
                <small>Category:
                    {{$t(`models.post.category.${$store.getters['application/constants'].posts.category[data.category]}`)}}
                </small>
                <strong>{{data.title}}</strong>
            </div>
            <hr v-if="data.pinned" />
            <read-more class="content" :text="data.content" :max-chars="512" more-str="Read more" less-str="Read less" />
            <hr v-if="data.pinned"/>
            <div class="execution" v-if="data.pinned">
                Execution {{execution}}
            </div>
            <div class="providers" v-if="data.pinned && data.providers && data.providers.length">
                Providers: {{data.providers.map(provider => provider.name).join(', ')}}
            </div>
            <media-gallery-carousel :media="data.media" :use-placeholder="false" height="320px" :autoplay="false" :gallery-options="{container: '#gallery'}" />
            <div class="likes" v-if="data.likes.length">
                <avatar :key="user.id" :name="user.name" :size="28" :src="user.avatar" v-for="user in data.likes" />
                <div class="users">
                    <template v-if="data.liked">You</template>
                    <template v-else>{{ data.likes[data.likes.length - 1].name }}</template>
                    <small>
                        <template v-if="data.likes_count > 1">
                            and {{data.likes_count - 1}} others liked this post
                        </template>
                        <template v-else>liked this post</template>
                    </small>
                </div>
            </div>
            <reactions :id="data.id" type="posts">
                <el-button @click="$refs.addComment.focus()" icon="ti-comment-alt" type="text">Comment</el-button>
            </reactions>
            <comments-list ref="comments" :id="data.id" type="post" :use-placeholder="false" />
            <add-comment ref="addComment" :id="data.id" type="post"/>
        </template>
    </card>
</template>

<script>
    import AgoMixin from 'mixins/agoMixin'
    import Card from 'components/Card'
    import Avatar from 'components/Avatar'
    import Reactions from 'components/Reactions'
    import AddComment from 'components/AddComment'
    import CommentsList from 'components/CommentsList'
    import MediaGalleryCarousel from 'components/MediaGalleryCarousel'
    import FormatDateTimeMixin from 'mixins/formatDateTimeMixin'
    import {format, isSameDay} from 'date-fns'

    export default {
        mixins: [AgoMixin, FormatDateTimeMixin],
        props: {
            data: {
                type: Object,
                required: true
            }
        },
        components: {
            Card,
            Avatar,
            Reactions,
            AddComment,
            CommentsList,
            MediaGalleryCarousel
        },
        methods: {
            showChildrenAddComment() {
                this.$refs.comments.showChildrenAddComment()
            }
        },
        computed: {
            isNewNeighbourType() {
                return this.$store.getters['application/constants'].posts.type[this.data.type] === 'new_neighbour'
            },
            execution() {
                const {execution_start, execution_end} = this.data

                const start = this.formatDatetime(execution_start)
                const end = format(execution_end, isSameDay(execution_start, execution_end) ? 'HH:mm':'DD.MM.YYYY HH:mm')

                return `${start} - ${end}`
            },
            tenant() {
                const {title, first_name, last_name} = this.data.tenant;

                return `${title}. ${first_name} ${last_name}`
            }
        }
    }
</script>

<style lang="scss" scoped>
    .el-card.post {
        color: lighten(#000, 32%);
        &.pinned {
            /deep/ .el-card__body {
                border-width: 8px;
                border-style: solid;
                border-image: linear-gradient(to bottom, darken(#fff, 4%), transparent) 1;
            }
        }

        hr {
            border-style: solid none none none;
            border-color: darken(#fff, 6%);
        }

        .user {
            display: flex;
            align-items: center;

            .name {
                font-size: 16px;
                line-height: 1.32;
                margin-left: 1em;

                small {
                    font-size: 80%;
                    display: block;
                    color: darken(#fff, 48%);
                }
            }
        }

        .title {
            font-weight: 500;
            margin: 8px 0;
            line-height: 1.32;

            small {
                font-size: 80%;
                font-weight: normal;
                display: block;
                color: darken(#fff, 48%);
            }
        }

        .execution {
            font-size: 12px;
            color: darken(#fff, 48%);
        }

        .media-gallery-carousel {
            margin: 12px -16px;
            box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
        }

        .likes {
            font-size: 14px;
            margin: 12px 0 -8px 0;
            display: flex;
            align-items: center;

            /deep/ .avatar {
                border: 2px #fff solid;

                &:not(:first-of-type) {
                    margin-left: -10px;
                }
            }

            .users {

                line-height: 1.32;

                small {
                    color: darken(#fff, 40%);
                }
            }
        }

        /deep/ .reactions {
            border-width: 1px;
            border-color: darken(#fff, 6%);
            border-style: solid none;
            margin: 16px -16px;
            padding: 12px 16px;
        }

        :global(.comments-list) {
            margin-bottom: 8px;
            > :global(.el-button) {
                padding-top: 0;
            }
        }

        .pinned {
            position: absolute;
            right: 2px;
            top: 2px;
            z-index: 1;
            overflow: hidden;
            width: 75px;
            height: 75px;
            text-align: right;

            span {
                font-size: 10px;
                font-weight: bold;
                color: #fff;
                text-transform: uppercase;
                text-align: center;
                line-height: 20px;
                transform: rotate(45deg);
                width: 100px;
                display: block;
                background: #6AC06F;
                background: linear-gradient(darken(#6AC06F, 10%) 0%, #6AC06F 100%);
                box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
                position: absolute;
                top: 19px;
                right: -21px;

                &:before {
                    content: "";
                    position: absolute;
                    left: 0px;
                    top: 100%;
                    z-index: -1;
                    border-left: 3px solid #6AC06F;
                    border-right: 3px solid transparent;
                    border-bottom: 3px solid transparent;
                    border-top: 3px solid #6AC06F;
                }

                &:after {
                    content: "";
                    position: absolute;
                    right: 0px;
                    top: 100%;
                    z-index: -1;
                    border-left: 3px solid transparent;
                    border-right: 3px solid #6AC06F;
                    border-bottom: 3px solid transparent;
                    border-top: 3px solid #6AC06F;
                }
            }
        }

        &:not(.new-neighbour) {
            .users {
                margin-left: 4px;

                small {
                    display: block;
                }
            }

            .content {
                margin: 16px 0;
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
        }

        &.new-neighbour {
            .content {
                margin-bottom: 8px;

                b {
                    text-transform: capitalize;
                }

                small {
                    display: block;
                    color: darken(#fff, 40%);
                }
            }

            .reactions {
                margin-bottom: -17px;
            }
        }
    }
</style>
