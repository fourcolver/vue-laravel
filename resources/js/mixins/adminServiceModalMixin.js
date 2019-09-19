import {mapActions} from 'vuex';
import {displayError, displaySuccess} from 'helpers/messages';

export default (config = {}) => {
    let mixin = {
        data() {
            return {
                selectedServiceRequest: {id: ''},
                showServiceMailModal: false,
                mailSending: false
            };
        },
        methods: {
            ...mapActions(['sendServiceRequestMail']),
            serviceWasSelected(service) {
                const foundService = _.find(this.services, {id: service});

                this.setSelectedServiceRequest(foundService);
                this.showServiceMailModal = true;
            },
            setSelectedServiceRequest(service) {
                this.selectedServiceRequest = Object.assign({}, service);
            },

            closeMailModal(reset = true) {
                this.showServiceMailModal = false;
                if (reset) {
                    this.setSelectedServiceRequest({});
                }
            },
            async sendServiceMail(serviceAttachModel) {
                if (serviceAttachModel.provider) {
                    this.mailSending = true;

                    try {
                        const resp = await this.sendServiceRequestMail({
                            request: this.model.id,
                            provider_id: serviceAttachModel.provider,
                            title: serviceAttachModel.subject,
                            body: serviceAttachModel.body,
                            cc: serviceAttachModel.cc,
                            bcc: serviceAttachModel.bcc,
                            assignee_ids: [serviceAttachModel.manager],
                            to: serviceAttachModel.to
                        });

                        if (resp) {
                            this.closeMailModal(false);
                            displaySuccess({
                                success: true,
                                message: this.$t('models.request.mail.success')
                            });
                        }

                    } catch (e) {
                        if (e.response.status == 422) {
                            displayError({
                                success: false,
                                message: this.$t('models.request.mail.fail_cc')
                            })
                        } else {
                            displayError(e)
                        }
                    } finally {
                        this.mailSending = false;
                    }
                }
            }
        }
    };

    if (config.mode) {
        switch (config.mode) {
            case 'add':
                mixin.methods = {
                    ...mixin.methods
                };
                break;
            case 'edit':
                mixin.methods = {
                    ...mixin.methods
                };

                break;
        }
    }


    return mixin;
};


