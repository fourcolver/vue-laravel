import itLocale from 'element-ui/lib/locale/lang/it';

export default  {
    ...itLocale,
    en: 'EN',
    fr: 'FR',
    it: 'IT',
    de: 'DE',
    timestamps:{
      hours: 'Orario',  
      days: 'Giorni',
      weeks: 'Settimane',
      months: 'Mesi',
      years: 'Anni'   
     },
    footerText: {
        companyName: 'Propify',
        leftSideText: 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero quis beatae officia saepe perferendis voluptatum minima eveniet voluptates dolorum, temporibus nisi maxime nesciunt totam repudiandae commodi sequi dolor quibusdam sunt.',
        allRightsSaved: 'Tutti i diritti riservati',
    },
    unauthenticated: 'Unauthenticated',
    logged_out: 'Logged out',
    logged_in: 'Logged in',
    invalid_credentials: 'Invalid Credentials',
    server_error: 'Server error',
    reset_password: 'Reset Password',
    reset_password_mail: 'Send reset password mail',
    back_to_login: 'Go back to login',
    forgot_password: 'Forgot password',
    remember_me: 'Remember me',
    password: 'Password',
    confirm_password: 'Confirm password',
    password_validation: {
        required: "Password is required",
        confirm: 'Please input the password again',
        match: 'The passwords aren\'t equal'
    },
    email: 'Email',
    email_validation: {
        required: 'Email is required',
        email: 'Please enter a valid Email',
    },
    login: 'Login',
    menu: {
        dashboard: 'Dashboard',
        news: 'News',
        requests: 'Requests',
        marketplace: 'Marketplace',
        settings: 'Settings',
        logout: 'Logout',
        profile: 'Profile',
        users: 'Users',
        employees: 'Employees',
        companies: 'Companies',
        admins: 'Admins',
        home_owners: 'Home Owners',
        about: 'About',
        feedback: 'Feedback'
    },
    dashboard:{
        statistics: 'Statistiche',
        requests_by_creation_date: 'Richieste per data di creazione',
        requests_by_status: 'Richieste per stato',
        requests_by_category: 'Richieste per categoria',
        each_hour_request: 'Ogni ora richiede',
        average_request_duration: 'Tempo di risoluzione'
    },
    support: "Support",
    actions: {
        label: "Operations",
        edit: 'Edit',
        delete: 'Delete',
        create: 'Create',
        view: 'Details'
    },
    models: {
        user: {
            name: 'Name',
            phone: 'Phone',
            date: 'Date',
            email: 'Email',
            id: 'ID',
            add: 'Add user',
            save: 'Save',
            validation: {
                name: {
                    required: 'Name is required'
                },
                role: {
                    required: 'Role is required'
                }
            }
        }
    },
    swal: {
        delete: {
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            confirmText: 'Yes, delete it!',
            deleted: 'Deleted successfully'
        },
        add: {
            added: 'Added successfully'
        }
    },
    roles: {
        label: 'Role',
        administrator: 'Administrator',
        homeowner: 'Home Owner',
        manager: 'Manager',
        registered: 'Registered',
        service: 'Service',
        super_admin: 'Super Admin',
    },
    settings: {
        notifications: "Notifications and language",
        admin: 'Admin notifications',
        news: 'News notifications',
        service: 'Service notifications',
        updated: 'Settings updated'
    }
}
