export default (config = {}) => {
    const {model = 'model', form = 'form', required = true, nestedModel = ''} = config;

    return {
        methods: {
            validatePassword(rule, value, callback) {
                let validateObject = this[model];

                if (nestedModel) {
                    validateObject = this[model][nestedModel];
                }

                if ((value === '' && required) || value === '' && validateObject.password_confirmation) {
                    callback(new Error('This field is required.'))
                } else {
                    this.$refs[form].validateField('password_confirmation');

                    callback();
                }

            },
            validateConfirmPassword(rule, value, callback) {
                let validateObject = this[model];

                if (nestedModel) {
                    validateObject = this[model][nestedModel];
                }

                if (required && value === '') {
                    callback(new Error('This field is required.'));
                } else if (value && !validateObject.password) {
                    this.$refs[form].validateField('password');
                } else if (value !== validateObject.password) {
                    callback(new Error('The passwords do not match.'));
                } else {
                    callback();
                }
            }
        }
    };
};