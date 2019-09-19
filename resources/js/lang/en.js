import enLocale from 'element-ui/lib/locale/lang/en';

export default {
    ...enLocale,
    en: 'EN',
    fr: 'FR',
    it: 'IT',
    de: 'DE',
    yes: 'Yes',
    timestamps:{
      hours: 'Hours',  
      days: 'Days',
      weeks: 'Weeks',
      months: 'Months',      
      years: 'Years'     
    },
    chooseLanguage: 'Choose Language',
    languages: {
        fr: 'Français',
        it: 'Italiano',
        de: 'Deutsch',
        en: 'English'
    },
    footerText: {
        companyName: 'Propify',
        leftSideText: 'Lorem ipsum, dolor sit amet consectetur adipisicing elit. Libero quis beatae officia saepe perferendis voluptatum minima eveniet voluptates dolorum, temporibus nisi maxime nesciunt totam repudiandae commodi sequi dolor quibusdam sunt.',
        allRightsSaved: 'All rights reserved',
    },
    days: {
        monday: 'Monday',
        tuesday: 'Tuesday',
        wednesday: 'Wednesday',
        thursday: 'Thursday',
        friday: 'Friday',
        saturday: 'Saturday',
        sunday: 'Sunday'
    },
    no: 'No',
    none: 'None',
    all: 'All',
    loadMore: 'Load more',
    account: "Account",
    unauthenticated: 'Unauthenticated',
    logged_out: 'Logged out',
    logged_in: 'Logged in',
    invalid_credentials: 'Invalid Credentials',
    server_error: 'Server error',
    reset_password: 'Reset Password',
    reset_password_mail: 'Send reset password mail',
    reset_password_mail_sent: 'Reset password mail sent, please check your inbox',
    back_to_login: 'Go back to login',
    forgot_password: 'Forgot password',
    remember_me: 'Remember me',
    password: 'Password',
    change_password: 'Change password',
    new_password: 'New password',
    old_password: 'Old password',
    new_password_confirmation: "New password confirmation",
    change: 'Change',
    cancel: 'Cancel',
    confirm: 'Confirm',
    confirm_password: 'Confirm password',
    incorrect_password: "Old password is incorect",
    password_changed: "Password changed successfully",
    details_saved: 'Details saved',
    password_validation: {
        required: "Password is required",
        confirm: 'Please input the password again',
        match: 'The passwords aren\'t equal',
        min: "Password must be at least 6 characters",
        old_password_min: "Old password must be at least 6 characters",
        old_password_required: 'Old password is required'
    },
    email: 'Email',
    email_validation: {
        required: 'Email is required',
        email: 'Please enter a valid Email',
    },
    token_invalid: "Invalid token",
    login: 'Login',
    menu: {
        dashboard: 'Dashboard',
        news: 'News',
        requests: 'Requests',
        all_requests: 'All requests',
        marketplace: 'Marketplace',
        settings: 'Settings',
        logout: 'Logout',
        profile: 'Profile',
        users: 'Users',
        employees: 'Managers',
        companies: 'Services',
        admins: 'Administrators',
        super_admins: 'Super admins',
        home_owners: 'Home Owners',
        registered: 'Registered',
        about: 'About',
        feedback: 'Feedback',
        tenants: 'Tenants',
        buildings: 'Buildings',
        all_buildings: 'All buildings',
        units: 'Units',
        addresses: 'Addresses',
        posts: 'News',
        districts: 'Districts',
        products: 'Products',
        requestCategories: 'Request categories',
        services: "Service Partners",
        activity: 'Activity',
        propertyManagers: 'Property managers',
        templates: 'Templates'
    },
    dashboard:{
        statistics: 'Statistics',
        requests_by_creation_date: 'Requests by creation date',
        requests_by_status: 'Requests by status',
        requests_by_category: 'Requests by category',
        each_hour_request: 'Each hour requests',
        average_request_duration: 'Resolution time'
    },
    pages: {
        profile: {
            pageTitle: 'Profile',
            profile: 'Profile',
            account: 'Account',
            security: 'Security',
            notifications: 'Notifications'
        },
        user: {
            title: 'Users'
        },
        request_activities: {
            title: 'Request activities'
        },
        tenant: {
            title: 'Tenants'
        }
    },
    support: "Support",
    actions: {
        label: "Operations",
        edit: 'Edit',
        add: 'Add',
        delete: 'Delete',
        create: 'Create',
        view: 'Details',
        save: 'Save',
        close: 'Close',
        saveAndClose: 'Save & Close',
        upload: 'Upload'
    },
    models: {
        user: {
            edit_action: 'Edit',
            delete: 'Delete',
            name: 'Name',
            phone: 'Phone',
            date: 'Date',
            email: 'Email',
            id: 'ID',
            add: 'Add user',
            save: 'Save',
            saved: 'User saved successfully',
            edit: 'Edit user',
            not_found: 'User not found',
            profile_image: 'Profile image',
            profile_text: 'Profile text',
            avatar_uploaded: 'Avatar uploaded',
            logo_uploaded: 'Logo uploaded',
            logo: 'Logo',
            address: 'Address',
            blank_pdf: 'Blank pdf',
            realEstateSaved: "Real Estate settings saved",
            validation: {
                name: {
                    required: 'Name is required'
                },
                role: {
                    required: 'Role is required'
                }
            }
        },
        tenant: {
            edit_title: 'Edit tenant',
            download_credentials: 'Download credentials',
            send_credentials: 'Send credentials',
            credentials_sent: 'Credentials sent',
            credentials_send_fail: 'Credentials file not found. Try updating the tenant password to regenerate it',
            credentials_download_failed: 'Credentials file not found. Try updating the tenant password to regenerate it',
            add: 'Add tenant',
            save: 'Save',
            update: 'Update',
            name: 'Name',
            first_name: 'First name',
            last_name: 'Last name',
            birth_date: 'Birth date',
            title: 'Title',
            mobile_phone: 'Mobile phone',
            work_phone: 'Work phone',
            email: 'Email',
            personal_phone: 'Personal phone',
            private_phone: 'Personal phone',
            created_at: 'Date',
            edit: 'Edit',
            delete: 'Delete',
            id: 'ID',
            details: 'Details',
            contract: 'Contract',
            posts: 'Posts',
            products: 'Products',
            requests: 'Requests',
            company: 'Company name',
            no_building: 'No building',
            building: {
                name: 'Building'
            },
            unit: {
                name: 'Unit'
            },
            search_building: 'Search building',
            search_unit: 'Search unit',
            confirmDelete: {
                title: "This will permanently delete the tenant.",
                text: 'Are you sure?'
            },
            validation: {
                first_name: {
                    required: 'First name is required'
                },
                last_name: {
                    required: 'Last name is required'
                },
                birth_date: {
                    required: 'Birth date is required'
                },
                building: {
                    required: 'Building is required',
                },
                unit: {
                    required: 'Unit is required',
                },
                title: {
                    required: 'Title is required',
                },
            },
            building_card: 'Assign unit',
            personal_details_card: 'Personal details',
            account_info_card: 'User login',
            contact_info_card: 'Contact details',
            personal_data: 'Personal data',
            my_documents: 'My documents',
            my_contract: 'My contract',
            contact_persons: 'My contacts',
            no_contacts: 'No contacts available',
            rent_end: 'Rent end',
            rent_start: 'Rent start',
            rent_contract: 'Rent contract',
            contact: {
                category: 'Category',
                name: 'Name',
                email: 'Email',
                phone: 'Phone'
            },
            titles: {
                mr: 'Mr.',
                mrs: 'Mrs.',
                company: 'Company'
            },
            status: {
                label: 'Status',
                active: 'Active',
                not_active: 'Not active'
            },
            confirmChange: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            }
        },
        building: {
            title: 'Buildings',
            edit_title: 'Edit Building',
            add: 'Add building',
            name: 'Name',
            cancel: 'Cancel',
            created_at: 'Date',
            edit: 'Edit',
            delete: 'Delete',
            units: 'Units',
            save: 'Save',
            saved: 'Building saved',
            floors: 'Floors',
            basement: 'Basement',
            attic: 'Attic',
            description: 'Description',
            floor_nr: 'Number of floors',
            label: "Label",
            address: 'Address',
            address_search: 'Please enter address',
            not_found: 'Building not found',
            house_rules: 'House rules',
            operating_instructions: 'Operating instructions',
            files: 'Files',
            add_files: 'Add files',
            add_companies: 'Add companies',
            companies: 'Services companies',
            no_services: 'No services added',
            details: 'Details',
            select_media_category: 'Selected media category',
            district: 'District',
            tenants: 'Tenants',
            managers: 'Managers',
            requests: 'Requests',
            house_nr: 'House Nr.',
            assign: 'Assign',
            assign_managers: 'Assign managers',
            unassign_manager: 'Unassign',
            managers_assigned: 'Managers assigned',
            occupied_units: "Ocuppied units",
            free_units: "Free units",
            manager: {
                unassigned: 'Manager unassigned'
            },
            document: {
                uploaded: 'Document uploaded',
                deleted: 'Document deleted'
            },
            service: {
                deleted: 'Service removed from this building'
            },
            confirmDelete: {
                title: "This will permanently delete the building.",
                text: 'Are you sure?'
            },
            validation: {
                name: {
                    required: 'Name is required'
                },
                floor_nr: {
                    required: 'Floor number is required'
                },
                description: {
                    required: 'Description is required'
                },
                label: {
                    required: 'Label is required'
                },
                address_id: {
                    required: 'Address is required'
                },
            },
            requestStatuses: {
                total: 'Total requests',
                received: 'Received requests',
                assigned: 'Assigned requests',
                in_processing: 'In processing requests',
                reactivated: 'Reactivated requests',
                done: 'Done requests',
                archived: 'Archived requests'
            },
            placeholders: {
                search: 'Search'
            },
            delete_building_modal: {
                title: "Delete Building(s)",
                description_unit: "Units are assigned to the selected property. If you want to delete the units as well, please activate the option below.",
                description_request: "Requests are assigned to the selected property. If you also want to delete request as well, please activate the option below.",
                description_both: "Units and requests are assigned to the selected property. If you also want to delete them, please activate the options below.",
                delete_units: "Delete Unit(s)",
                dont_delete_units: "Don't Delete Unit(s)",
                delete_requests: "Delete Request(s)",
                dont_delete_requests: "Don't Delete Request(s)"
            }
        },
        unit: {
            title: 'Units',
            not_found: 'Unit not found',
            add: 'Add unit',
            name: 'Unit number',
            created_at: 'Date',
            edit: 'Edit',
            delete: 'Remove',
            save: 'Save',
            saved: "Unit saved",
            floor: 'Floor',
            sq_meter: 'Sq Meter',
            room_no: 'Number of rooms',
            monthly_rent: 'Monthly rent',
            building_search: 'Please enter a building name and select it',
            building: 'Building',
            description: 'Description',
            basement: 'Basement',
            attic: 'Attic',
            requests: 'Requests',
            tenant: 'Tenant',
            empty_requests: 'No requests',
            assigned_tenant: 'Assigned tenant',
            type: {
                label: 'Type',
                apartment: 'Apartment',
                business: 'Business'
            },
            confirmDelete: {
                title: "This will permanently delete the unit.",
                text: 'Are you sure?'
            },
            validation: {
                name: {
                    required: 'Name is required'
                },
                building: {
                    required: 'Building is required'
                },
                monthly_rent: {
                    required: 'Monthly rent is required'
                },
                floor: {
                    required: 'Floor is required'
                },
                room_no: {
                    required: 'Room number is required'
                },
                description: {
                    required: 'Description is required'
                },
            },
            placeholders: {
                search: 'Search',
                select: 'Select'
            }
        },
        address: {
            add: 'Add address',
            created_at: 'Date',
            name: 'Address',
            edit: 'Edit',
            delete: 'Remove',
            save: 'Save',
            city: 'City',
            country: 'Country',
            street: 'Street',
            street_nr: 'Street Nr.',
            zip: 'Zip',
            not_found: 'Address not found',
            saved: 'Address saved',
            confirmDelete: {
                title: "This will permanently delete the address.",
                text: 'Are you sure?'
            },
            state: {
                label: 'State'
            },
            validation: {
                state: {
                    required: 'State is required'
                },
                city: {
                    required: 'City is required'
                },
                street: {
                    required: 'Street is required'
                },
                street_nr: {
                    required: 'Street number is required'
                },
                zip: {
                    required: 'Zip is required'
                }
            }
        },
        post: {
            title: 'News',
            title_label: 'Title',
            content: 'Content',
            preview: 'Preview',
            add: 'Add',
            add_pinned: 'Add pinned post',
            save: 'Save',
            edit: 'Edit',
            edit_title: 'Edit post',
            show: 'Details',
            user: "User",
            delete: 'Delete',
            likes: 'Likes',
            details: 'Post Details',
            published_at: 'Published',
            publish: 'Publish',
            unpublish: 'Unpublish',
            buildings: 'Buildings',
            pinned: 'Pinned',
            notify_email: 'Notify email',
            pinned_to: 'Pinned to',
            comments: 'Comments',
            images: 'Images',
            details_title: "Details",
            placeholders: {
                buildings: "Choose buildings",
                search: 'Search',
                search_provider: 'Search provider'
            },
            media: {
                deleted: 'Media deleted',
                removed: 'Media removed'
            },
            type: {
                label: 'Type',
                article: 'Article',
                new_neighbour: 'New neighbour',
                pinned: 'Pinned',
            },
            status: {
                label: 'Status',
                new: 'New',
                published: 'Published',
                unpublished: 'Unpublished',
                not_approved: 'Not approved'
            },
            visibility: {
                label: 'Visibility',
                address: 'Address',
                district: 'District',
                all: 'All'
            },
            confirmChange: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            },
            assignmentTypes: {
                building: 'Building',
                district: 'District'
            },
            assignType: 'Type',
            unassign: 'Unassign',
            assign: 'Assign',
            attached: {
                building: 'Building assigned',
                district: 'District assigned',
                provider: 'Provider assigned'
            },
            detached: {
                building: 'Buiding unassigned',
                district: 'District unassigned',
                provider: 'Provider unassigned'
            },
            buildingAlreadyAssigned: 'Building is already inside on a district',
            confirmUnassign: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            },
            execution_interval: {
                label: 'Execution interval',
                end: 'Execution End',
                start: 'Execution Start',
                separator: 'To'
            },
            category: {
                label: 'Category',
                general: 'General',
                maintenance: 'Maintenance',
                electricity: 'Electricity',
                heating: 'Heating',
                sanitary: 'Sanitary'
            }
        },
        service: {
            title: 'Services',
            add_title: 'Add Service',
            edit_title: 'Edit Service',
            edit: 'Edit',
            delete: 'Delete',
            category: 'Category',
            electrician: "Electrician",
            heating_company: 'Heating company',
            lift: 'Lift',
            sanitary: 'Sanitary',
            key_service: 'Key service',
            caretaker: 'Caretaker',
            real_estate_service: 'Real estate service',
            deleted: 'Deleted',
            name: 'Name',
            requests: 'Requests',
            contact_details: 'Contact details',
            user_credentials: 'User credentials',
            company_details: 'Company details',
            assignmentTypes: {
                building: 'Building',
                district: 'District'
            },
            assignType: 'Type',
            unassign: 'Unassign',
            assign: 'Assign',
            attached: {
                building: 'Building assigned',
                district: 'District assigned'
            },
            detached: {
                building: 'Buiding unassigned',
                district: 'District unassigned'
            },
            buildingAlreadyAssigned: 'Building is already inside on a district',
            confirmUnassign: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            },
            placeholders: {
                search: 'Search',
                category: 'Select category'
            }
        },
        district: {
            title: 'Districts',
            name: 'Name',
            description: 'Description',
            add: 'Add district',
            edit: 'Edit district',
            edit_action: 'Edit',
            delete: 'Delete',
            cancel: 'Cancel',
            required: 'This field is required',
            details: 'Details',
            buildings: 'Buildings'
        },
        realEstate: {
            title: 'Settings real estate',
            details: 'Details',
            settings: 'Settings',
            district_enable: 'District',
            marketplace_approval_enable: 'Enable Market',
            news_approval_enable: 'News approval',
            comment_update_timeout: 'Comment update timeout',
            closed: 'Closed',
            schedule: 'Schedule',
            endTime: 'End time',
            startTime: 'Start time',
            to: 'To',
            categories: 'Categories',
            templates: 'Templates',
            contact_enable: "Enable 'My contacts'",
            cleanify_email: 'Cleanify email',
            iframe_url: {
                label: 'Iframe URL',
                validation: 'Iframe URL should be a valid URL'
            }
        },
        request: {
            edit: 'Edit',
            delete: 'Delete',
            deleted: 'Deleted',
            title: 'Requests',
            created: 'Created',
            prop_title: 'Title',
            description: 'Description',
            category: 'Category',
            address: 'Address',
            edit_title: 'Edit request',
            add_title: 'Add request',
            tenant: 'Tenant',
            due_date: 'Due date',
            closed_date: 'Closed date',
            service: 'Service',
            created_by: 'Created by',
            is_public: 'Public',
            comments: 'Comments',
            assigned_to: 'Assigned to',
            assign_providers: 'Assign providers',
            assign_managers: 'Assign managers',
            unassign: 'Unassign',
            notify: 'Notify',
            public_legend: 'Set this option to make the request visible to all tenant neighbours',
            conversation: 'Conversation',
            open_conversation: 'Open',
            other_recipients: 'Other recipients',
            recipients: 'Recipients',
            assign: 'Assign',
            images: 'Images',
            no_images_message: 'No files uploaded',
            request_details: 'Request details',
            internal_notices: 'Internal notices',
            assignmentTypes: {
                services: 'Services',
                managers: 'Managers'
            },
            media: {
                removed: 'Media removed',
                deleted: 'Media deleted',
                delete: 'Delete'
            },
            priority: {
                label: 'Priority',
                urgent: 'Urgent',
                low: 'Low',
                normal: 'Normal'
            },
            defect_location: {
                label: 'Defect location',
                apartment: 'Apartment',
                building: 'Building',
                environment: 'Environment'
            },
            qualification: {
                label: 'Qualification',
                none: 'None',
                optical: 'Optical',
                sia: 'Sia',
                '2_year_warranty': '2 Year Warranty',
                cost_consequences: 'Cost consequences'
            },
            status: {
                label: 'Status',
                received: 'Received',
                in_processing: 'In processing',
                assigned: 'Assigned',
                done: 'Done',
                reactivated: 'Reactivated',
                archived: 'Archived'
            },
            placeholders: {
                category: 'Select category',
                priority: 'Select priority',
                defect_location: 'Select defect location',
                qualification: 'Select qualification',
                status: 'Select status',
                due_date: 'Pick due date',
                tenant: 'Search for a tenant',
                service: 'Search for a service',
                propertyManagers: 'Search for managers',
                search: 'Search',
                visibility: 'Select visibility'
            },
            confirmChange: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            },
            confirmUnassign: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            },
            mail: {
                body: 'Body',
                subject: 'Subject',
                to: 'To',
                title: 'Notify service',
                notify: 'Send Email',
                bodyPlaceholder: 'Please write your message here',
                provider: 'Provider',
                manager: 'Manager',
                cancel: 'Cancel',
                send: 'Send',
                cc: 'CC',
                bcc: 'BCC',
                success: 'Notification mail sent successfully',
                validation: {
                    required: 'This field is required',
                    email: 'This field should be a valid email'
                },
                fail_cc: "CC/BCC/TO fields must be valid emails"
            },
            attached: {
                services: 'Provider attached successfully',
                managers: 'Manager attached successfully'
            },
            detached: {
                service: 'Provider detached successfully',
                manager: 'Manager detached successfully'
            },
            userType: {
                label: 'Type',
                provider: 'Service',
                user: 'Manager'
            },
            visibility: {
                label: 'Visibility',
                tenant: 'Private',
                district: 'District',
                building: 'Building',
            },
            requestID: "Request ID",
            requestCategory: "Request Category",
        },
        requestCategory: {
            title: 'Request categories',
            add: 'Add category',
            edit: 'Edit',
            delete: 'Delete',
            name: 'Name',
            cancel: 'Cancel',
            required: 'This field is required',
            parent: 'Parent category'
        },
        propertyManager: {
            title: 'Property managers',
            title_label: 'Title',
            add: 'Add property manager',
            save: 'Save',
            edit: 'Edit',
            edit_title: 'Edit property manager',
            delete: 'Delete',
            firstName: 'First name',
            lastName: 'Last name',
            name: 'Name',
            profession: 'Profession',
            slogan: 'Slogan',
            linkedin_url: 'Linkedin URL',
            xing_url: 'Xing URL',
            email: "Email",
            password: 'Password',
            confirm_password: 'Confirm password',
            phone: 'Phone',
            building_card: 'Assign buildings',
            details_card: 'Details',
            no_buildings: 'There are no buildings assigned',
            add_buildings: 'Add buildings',
            buildings_search: 'Search for buildings',
            districts: 'Districts',
            requests: 'Requests',
            assign: 'Assign',
            unassign: 'Unassign',
            delete_with_reassign_modal: {
                title : 'Delete & reassign buildings',
                description: 'The selected property manager is linked to properties. You can assign the properties to another person. To do this, select a property manager from the list.',
                search_title: 'Search Property Manager',
            },
            delete_without_reassign: 'Delete',
            profile_card: 'User Profile',
            social_card: 'Social Media',
            deleted: 'Deleted',
            titles: {
                mr: 'Mr.',
                mrs: 'Mrs.'
            },
            assignmentTypes: {
                building: 'Building',
                district: 'District'
            },
            assignType: 'Type',
            placeholders: {
                search: 'Search'
            },
            attached: {
                building: 'Building assigned',
                district: 'District assigned',
            },
            detached: {
                building: 'Buiding unassigned',
                district: 'District unassigned'
            },
            buildingAlreadyAssigned: 'Building is already inside on a district',
            confirmUnassign: {
                title: 'Are you sure you want to continue?',
                warning: 'Warning',
                confirmBtnText: 'Ok',
                cancelBtnText: 'Cancel'
            }
        },
        product: {
            title: 'Products',
            add: 'Add product',
            edit_title: 'Edit product',
            edit: 'Edit',
            delete_action: 'Delete',
            show: 'Details',
            details: 'Product details',
            delete: 'Delete product',
            content: "Content",
            product_title: 'Title',
            published_at: 'Published',
            publish: 'Publish',
            unpublish: 'Unpublish',
            likes: 'Likes',
            save: 'Save',
            comments: 'Comments',
            user: 'User',
            contact: 'Contact',
            price: 'Price',
            media: {
                removed: 'Media removed',
                deleted: 'Media deleted',
            },
            type: {
                label: 'Type',
                sell: 'Sell',
                lend: 'Lend',
                service: 'Service',
                giveaway: 'Give away'
            },
            status: {
                label: 'Status',
                published: 'Published',
                unpublished: 'Unpublished'
            },
            visibility: {
                label: 'Visibility',
                address: 'Address',
                district: 'District',
                all: 'All'
            }
        },
        template: {
            name: 'Name',
            edit: 'Edit',
            delete: 'Delete',
            add: 'Add',
            title: 'Templates',
            subject: 'Subject',
            body: 'Body',
            category: 'Category',
            tags: 'Tags',
            placeholders: {
                category: 'Choose category'
            }
        },
        cleanify: {
            pageTitle: 'Cleanify request',
            title: 'Title',
            lastName: 'Last name',
            firstName: 'First name',
            address: 'Address',
            city: 'City',
            zip: 'Zip',
            email: 'Email',
            phone: 'Phone',
            save: 'Send request',
            success: 'Cleanify request sent successfully',
            terms_and_conditions: 'Accept Terms & Conditions',
            terms_text: "Terms text here, long text"
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
        marketplace: 'Marketplace notifications',
        service: 'Service notifications',
        updated: 'Settings updated',
        language: 'Language',
        summary: {
            label: "Summary statistics",
            daily: "Daily",
            monthly: "Monthly",
            yearly: "Yearly"
        }
    },
    search: {
        placeholder: 'Search'
    },
    filters: {
        header: 'Filters',
        districts: 'Districts',
        buildings: 'Buildings',
        requests: 'Requests',
        open_requests: 'Open requests',
        units: 'Units',
        states: 'States',
        status: 'Status',
        search: 'Search',
        requestStatus: 'Request status',
        propertyManagers: 'Property Manager',
        categories: 'Categories',
        created_from: 'Created from',
        created_to: 'Created to',
        services: 'Services',
        tenant: 'Type tenants'
    },
    errors: {
        files_extension_images: 'Only jpg and png files accepted'
    },
    validation: {
        general: {
            required: 'This field is required'
        },
        price: {
            valid: 'Please enter a valid price',
            required: 'Price is required',
        },
        firstName: {
            required: 'First name is required'
        },
        lastName: {
            required: 'Last name is required'
        },
        phone: {
            required: 'Phone is required'
        },
        address: {
            required: 'Address is required'
        },
        zip: {
            required: 'Zip is required'
        },
        city: {
            required: 'City is required'
        },
        title: {
            required: 'Title is required'
        },
        terms: {
            required: 'Please approve with terms and conditions'
        }
    },

    layouts: {
        tenant: {
            menu: {
                logout: 'Logout'
            },
            sidebar: {
                dashboard: 'Dashboard',
                myTenancy: 'My tenancy',
                myPersonalData: 'My personal data',
                myRecentContract: 'My recent contract',
                myDocuments: 'Documents',
                myContactPersons: 'Contact persons',
                posts: 'News',
                requests: 'Requests',
                products: 'Marketplace',
                settings: 'Settings'
            }
        }
    },
    components: {
        common: {
            audit: {

            },
            commentsList: {
                loading: 'Loading...',
                loadMore: {
                    simple: 'Load {count} more',
                    detailed: 'Load {count} more comments'
                },
                emptyPlaceholder: {
                    title: 'There are no messages yet...',
                    description: 'Start messaging by using the below form and press enter.'
                }
            },
            comment: {
                updateShortcut: 'or use {shortcut} shortcut',
                updateOrCancel: '{update} or press {esc} to {cancel}',
                update: 'update',
                esc: 'ESC',
                cancel: 'cancel',
                addChildComment: 'Comment',
                loadMore: 'Load 1 more comment | Load {count} more comments',
                deletedCommentPlaceholder: 'This comment was deleted.'
            },
            addComment: {
                placeholder: 'Type a comment...',
                tooltipTemplates: 'Choose a template',
                loadingTemplates: 'Loading templates...',
                saveShortcut: 'or use {shortcut} shortcut',
                emptyTemplatesPlaceholder: 'No templates available.'
            }
        },
        tenant: {
            weatherWidget: {
                minTemp: 'min',
                maxTemp: 'max',
                wind: 'wind',
                cloudiness: 'cloudiness',
                humidity: 'humidity',
                pressure: 'pressure'
            }
        },
        admin: {

        }
    },

    views: {
        tenant: {
            my: {
                personal: {
                    title: 'Personal data',
                    description: 'My personal details.',
                    placeholder: {
                        title: 'No personal data available.',
                        description: 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'
                    }
                }
            }
        }
    },
    dateTimeFormat: '{date} at {time}'
}
