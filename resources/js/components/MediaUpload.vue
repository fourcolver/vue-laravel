<template>
    <div class="media-upload">
        <upload ref="upload" :drop="drop" :drop-directory="dropDirectory" :size="size" :maximum="maximum" :multiple="multiple" :value="value" :input-id="`upload-${$_uid}`" @input="onUpload" @input-filter="onUploadFilter" />
        <gallery :images="images" :index="galleryIndex" :options="galleryOptions" />
        <draggable class="media-draggable" :list="value" :animation="240" :disabled="!drag || loading" ghostClass="ghost">
            <transition-group class="media-list" type="transition" name="flip-list" tag="div">
                <div class="media-item" v-for="(file, idx) in value" :key="file.id" :class="{draggable: drag && !loading}" :style="mediaItemStyle">
                    <div class="media-content">
                        <el-image :src="file.file.blob" fit="cover" :alt="file.name" v-if="isFileImage(file)" />
                        <template v-else>
                            <i class="media-icon ti-file"></i>
                            <div class="media-filename">{{file.name}}</div>
                        </template>
                        <div class="media-state" v-loading="loading" element-loading-background="rgba(0, 0, 0, .56)" v-if="loading"></div>
                        <div class="media-actions" v-else>
                            <div class="el-icon-zoom-in" @click="openFile(file, idx)" v-if="canFileBePreviewed(file)"></div>
                            <div class="el-icon-delete remove" @click="removeFile(idx)"></div>
                        </div>
                    </div>
                </div>
                <slot name="trigger" :mediaItemStyle="mediaItemStyle" :triggerSelect="triggerSelect">
                    <el-button key="trigger" class="trigger" icon="el-icon-plus" :disabled="loading" @click="triggerSelect">
                        Drop files or click here to select...
                    </el-button>
                </slot>
            </transition-group>
        </draggable>
        <div v-show="$refs.upload && $refs.upload.dropActive" class="drop-active">
            <i class="el-icon-upload"></i>
            Drop your files here
            <small>Only the files with the following <b>{{allowedExtensions}}</b> extensions are allowed.</small>
        </div>
    </div>
</template>

<script>
    import Gallery from './MediaGallery'
    import draggable from 'vuedraggable'
    import Upload from 'vue-upload-component'

    export default {
        props: {
            value: {
                type: Array
            },
            loading: {
                type: Boolean,
                default: false
            },
            cols: {
                type: Number,
                default: 4
            },
            size: {
                type: Number,
                default: 0
            },
            drag: {
                type: Boolean,
                default: true
            },
            drop: {
                type: Boolean,
                default: true
            },
            dropDirectory: {
                type: Boolean,
                default: true
            },
            maximum: {
                type: Number,
                default: 0
            },
            multiple: {
                type: Boolean,
                default: true
            },
            galleryOptions: {
                type: Object,
                default: () => ({})
            },
            allowedTypes: {
                type: Array,
                validator: types => types.length > 1,
                default: () => ([
                    'image/jpeg',
                    'image/jpg',
                    'image/png',
                    'application/pdf'
                ])
            }
        },
        components: {
            Upload,
            Gallery,
            draggable
        },
        data () {
            return {
                galleryIndex: null
            }
        },
        methods: {
            triggerSelect () {
                const el = document.getElementById(`upload-${this.$_uid}`)

                if (el) {
                    el.click()
                }
            },
            onUpload (value) {
                this.$emit('input', value)
            },
            onUploadFilter (newFile, oldFile, prevent) {
                if (newFile) {
                    if (!this.allowedTypes.includes(newFile.type)) {
                        return prevent()
                    }

                    if (this.size && this.size < newFile.size) {
                        this.$message.closeAll()

                        this.$message.error(`Oops, the file is over maximum allowed of ${this.formatBytes(this.size)}.`, {
                            offset: 88
                        })
                        
                        return prevent()
                    }
                    
                    const reader = new FileReader();
    
                    reader.readAsDataURL(newFile.file)
                    reader.onload = () => newFile.file.src = /,(.+)/.exec(reader.result)[1]

                    newFile.file.blob = URL.createObjectURL(newFile.file)
                }
            },
            clear () {
                this.$refs.upload.clear();
            },
            removeFile (id) {
                this.$refs.upload.remove(id)
            },
            openFile (file, idx) {
                if (this.isFileImage(file)) {
                    this.galleryIndex = idx;
                } else {
                    if (this.canFileBePreviewed(file)) {
                        window.open(file.file.blob)
                    } else {
                        this.$message.warning('This file cannot be previewed', {
                            duration: 2400,
                            offset: 88
                        })
                    }
                }
            },
            removeFile (idx) {
                this.value.splice(idx, 1)
            },
            isFileImage ({type}) {
                return type.includes('image/')
            },
            canFileBePreviewed (file) {
                return this.isFileImage(file) || ['application/pdf'].includes(file.type)
            },
            formatBytes (bytes, decimals = 2) {
                if (bytes === 0) return '0 Bytes'

                const k = 1024
                const dm = decimals < 0 ? 0 : decimals
                const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB']

                const i = Math.floor(Math.log(bytes) / Math.log(k))

                return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i]
            }
        },
        computed: {
            images () {
                return this.value
                    .filter(({type}) => type.includes('image/'))
                    .map(({file}) => file.blob)
            },
            mediaItemStyle () {
                const width = 100 / this.cols + '%'

                return {
                    width,
                    padding: `calc(${width} - 10px) 0 0 0`,
                    margin: this.cols > 1 ? '4px' : '0px',
                    flexBasis: `calc(${width} - ${this.cols > 1 ? '8px' : '0px'})`
                }
            },
            allowedExtensions () {
                return this.allowedTypes
                    .map(ext => ext.replace(/^.*\/(.*)$/, "*.$1"))
                    .join(', ');
            },
        }
    }
</script>

<style lang="scss" scoped>
    .flip-list-move {
        transition: transform .56s;
    }

    .media-upload {
        width: 100%;
        display: flex;
        flex-direction: column;
        .media-draggable {
            margin: -4px;
            .media-list {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                .media-item {
                    position: relative;
                    will-change: transform;
                    .media-content {
                        position: absolute;
                        top: 0;
                        left: 0;
                        bottom: 0;
                        right: 0;
                        width: 100%;
                        height: 100%;
                        cursor: pointer;
                        box-sizing: border-box;
                        border: 1px darken(#fff, 12%) solid;
                        box-shadow: 0 1px 3px transparentize(#000, .88), 0 1px 2px transparentize(#000, .76);
                        overflow: hidden;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        text-align: center;
                        border-radius: 4px;
                        word-break: break-word;
                        hyphens: auto;
                        .media-icon {
                            font-size: 24px;
                            margin: 4px;
                        }
                        .media-filename {
                            overflow: hidden;
                            min-width: 0;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            width: 100%;
                            padding: 4px;
                            box-sizing: border-box;
                        }
                        .media-state {
                            position: absolute !important;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: #fff;
                        }
                        .media-actions {
                            background-color: transparentize(#000, .56);
                            position: absolute;
                            top: 0;
                            left: 0;
                            width: 100%;
                            height: 100%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            opacity: 0;
                            transition: opacity .32s cubic-bezier(.17,.67,1,1.23);
                            div {
                                color: #fff;
                                font-size: 20px;
                                transition-property: color, font-size;
                                transition-duration: .24s;
                                opacity: .72;
                                cursor: pointer;
                                &:hover {
                                    opacity: 1;
                                }
                                &.remove:hover {
                                    color: red;
                                }
                            }
                            div + div {
                                margin-left: 8px;
                            }
                        }
                        &:hover .media-actions {
                            opacity: 1;
                        }
                    }
                    &.placeholder .media-content {
                        border-width: 2px;
                        border-style: dashed;
                    }
                    &.draggable .media-content {
                        cursor: move;
                    }
                    &.ghost .media-content {
                        opacity: .56;
                    }
                }
            }
        }
        .trigger {
            width: calc(100% - 8px);
            border-width: 2px;
            border-style: dashed;
            order: -1;
            margin: 4px auto;
            position: relative;
        }
        .drop-active {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 12px;
            z-index: 9;
            background-color: transparentize(#fff, .08);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: lighten(#000, 28%);
            font-size: 24px;
            line-height: 1.24;
            border: 2px #6AC06F dashed;
            box-sizing: border-box;
            text-align: center;
            i {
                color: #6AC06F;
                font-size: 56px;
                margin: 4px;
            }
            small {
                font-size: 56%;
                color: darken(#fff, 48%);
            }
        }
    }
</style>
