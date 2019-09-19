<template>
    <el-dialog
        :close-on-click-modal="false"
        :title="selectedTitle"
        :visible="showServiceMailModal"
        @close="close"
        v-loading="mailSending"
        id="service-attach-modal"
    >   
        <el-tabs v-model="activeName">
            <el-tab-pane :label="$t('models.request.mail.notify')" name="notify">
                <span slot="label"><i class="el-icon-message"></i> {{$t('models.request.mail.notify')}}</span>                
                <el-form :model="model" :rules="validationRules" ref="form">
                    <el-collapse v-model="activeNames">
                        <el-collapse-item :title="$t('models.request.recipients')" class="collapse-item"
                                          name="recipients">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.mail.provider')" prop="provider">
                                        <el-select
                                            @change="fetchConversation"
                                            style="width: 100%"
                                            v-model="model.provider"
                                        >
                                            <el-option :key="provider.id" :label="provider.name" :value="provider.id"
                                                       v-for="provider in providers"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.mail.manager')">
                                        <el-select
                                            style="width: 100%"
                                            v-model="model.manager"
                                        >
                                            <el-option :key="manager.id" :label="manager.name" :value="manager.id"
                                                       v-for="manager in managers"></el-option>
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-collapse-item>
                        <el-collapse-item :title="$t('models.request.other_recipients')" name="cc">
                            <el-row :gutter="20">
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.mail.to')" prop="to">
                                        <el-input autocomplete="off" type="email" v-model="model.to"></el-input>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.mail.cc')" prop="cc">
                                        <el-select
                                            :automatic-dropdown="false"
                                            allow-create
                                            default-first-option
                                            filterable
                                            multiple
                                            style="width: 100%"
                                            v-model="model.cc"
                                        >
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                                <el-col :md="12">
                                    <el-form-item :label="$t('models.request.mail.bcc')" prop="bcc">
                                        <el-select
                                            :automatic-dropdown="false"
                                            allow-create
                                            default-first-option
                                            filterable
                                            multiple
                                            style="width: 100%"
                                            v-model="model.bcc"
                                        >
                                        </el-select>
                                    </el-form-item>
                                </el-col>
                            </el-row>
                        </el-collapse-item>
                    </el-collapse>
                    <el-form-item :label="$t('models.request.mail.subject')" prop="subject">
                        <el-input autocomplete="off" type="text" v-model="model.subject"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <quill-editor ref="quillEditor"
                                      v-model="model.body"
                        >
                        </quill-editor>
                    </el-form-item>
                </el-form>
            </el-tab-pane>
            <el-tab-pane :label="$t('models.request.conversation')" name="conversation"
                         v-if="model.provider && currentConversation && shouldFetchConversation">
                <span slot="label"><i class="ti-comment"></i> {{$t('models.request.conversation')}}</span>
                <chat :id="currentConversation" ref="chat" type="conversation" class="request-chat"/>
            </el-tab-pane>
        </el-tabs>

        <span class="dialog-footer" slot="footer">
                <el-button @click="close">{{$t('models.request.mail.cancel')}}</el-button>
                <el-button :disabled="!isValidForm" @click="send" type="primary"
                           v-if="activeName === 'notify'">{{$t('models.request.mail.send')}}</el-button>
          </span>
    </el-dialog>
</template>
<script>
    // require styles
    import 'quill/dist/quill.core.css'
    import 'quill/dist/quill.snow.css'
    import 'quill/dist/quill.bubble.css'

    import {quillEditor} from 'vue-quill-editor'
    import Chat from 'components/Chat2';


    export default {
        components: {
            quillEditor,
            Chat
        },
        props: {
            providers: {
                type: Array,
                required: true
            },
            managers: {
                type: Array,
                required: true
            },
            selectedServiceRequest: {
                type: Object,
                required: true
            },
            showServiceMailModal: {
                type: Boolean,
                default: false
            },
            mailSending: {
                type: Boolean,
                default: false
            },
            conversations: {
                type: Array,
                default() {
                    return []
                }
            },
            requestData: {
                type: Object,
                default: {}
            },
            address: {
                type: Object,
                default: {}
            }
        },
        data() {
            return {
                model: {
                    to: '',
                    cc: [],
                    bcc: [],
                    subject: '',
                    body: '',
                    provider: '',
                    manager: ''
                },
                activeNames: ['recipients', 'mail'],
                activeName: 'notify',
                validationRules: {
                    subject: [
                        {
                            required: true,
                            message: this.$t("models.request.mail.validation.required")
                        }
                    ],
                    body: [
                        {
                            required: true,
                            message: this.$t('models.request.mail.validation.required')
                        }
                    ],
                    provider: [
                        {
                            required: true,
                            message: this.$t('models.request.mail.validation.required')
                        }
                    ],
                    to: [
                        {
                            type: 'email',
                            message: this.$t('models.request.mail.validation.email')
                        }
                    ]
                },
                shouldFetchConversation: true
            }
        },
        computed: {
            isValidForm() {
                return this.model.provider && !_.isEmpty(this.model.body) && !_.isEmpty(this.model.subject);
            },
            currentConversation() {
                if (!this.model.provider) {
                    return false;
                }

                const selectedProvider = this.providers.find((provider) => {
                    return provider.id === this.model.provider;
                });

                if (!selectedProvider) {
                    return false;
                }

                const foundConversation = this.conversations.find((conversation) => {
                    return conversation.user.id === selectedProvider.user.id;
                });

                if (!foundConversation) {
                    return false;
                }

                return foundConversation.id;
            },
            selectedTitle () {
                if(this.requestData.tenant && this.address) {
                    return this.requestData.tenant.first_name + " " 
                        + this.requestData.tenant.last_name 
                        + ", " + this.address.street 
                        + " " + this.address.street_nr
                        + " " + this.address.city
                        + " [ " + this.requestData.service_request_format
                        +" | " + this.requestData.category +" ]";
                }else {
                    return "";
                }
            }
        },
        methods: {
            close() {
                this.$refs.form.resetFields();
                this.model.provider = '';
                this.model.manager = '';
                this.$emit('close');
                setTimeout(() => {
                    this.activeName = 'notify';
                }, 300)
            },
            send() {
                this.$refs.form.validate((valid) => {
                    if (!valid || !this.isValidForm) {
                        return false;
                    }

                    this.$emit('send', this.model);
                })
            },
            fetchConversation() {
                this.shouldFetchConversation = false;
                this.$nextTick(() => {
                    this.shouldFetchConversation = true;
                });
            }
        },
        watch: {
            showServiceMailModal(newVal) {
                const prop = this.selectedServiceRequest.uType === 1 ? 'provider' : 'manager';
                if (newVal) {
                    this.model[prop] = this.selectedServiceRequest.id;
                } else {
                    this.model[prop] = '';
                }
            }
        }
    };
</script>
<style>
    .ql-container.ql-snow, .ql-editor {
        min-height: 250px;
    }

    #tab-conversation span i {
        font-size: 12px !important;
    }

    .collapse-item .el-collapse-item__content {
        padding-bottom: 0;
    }

    .request-info-row {
        margin-bottom: 22px;
    }

    .request-chat .el-textarea .el-textarea__inner{
        min-height: 56px !important;
    }
    #service-attach-modal .el-dialog__header  {
        border-bottom: 1px solid #EBEEF5;
    }

    #service-attach-modal .el-dialog__body {
        padding: 10px 20px 30px;
    }

    #service-attach-modal .el-collapse {
        border-top: none;
    }

    #service-attach-modal .el-tabs__header{
        margin-bottom: 0;
    }
</style>
