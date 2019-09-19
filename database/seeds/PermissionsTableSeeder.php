<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    const ps = [
        // User
        ['list-user', 'List Users', 'list all users'],
        ['view-user', 'View Users', 'view users'],
        ['post-user', 'Post User', 'add user'],
        ['edit-user', 'Edit User', 'edit existing user'],
        ['delete-user', 'Delete User', 'delete existing user'],
        ['edit-user_setting', 'Edit user setting', 'edit user setting'],
        ['view-user_setting', 'View user setting', 'view user setting'],
        // Tenant
        ['list-tenant', 'List Tenants', 'list tenants'],
        ['view-tenant', 'View Tenant', 'view tenant'],
        ['post-tenant', 'Post Tenant', 'add tenant'],
        ['edit-tenant', 'Edit Tenant', 'edit existing tenant'],
        ['delete-tenant', 'Delete Tenant', 'delete existing tenant'],
        ['send_credentials-tenant', 'Send credentials', 'Send tenant credentials'],
        ['download_credentials-tenant', 'Download credentials', 'Download tenant credentials'],
        // Comment
        ['post-comment', 'Post Comment', 'add comment'],
        ['edit-comment', 'Edit Comment', 'edit existing comment'],
        // Post
        ['list-post', 'List Posts', 'list all posts'],
        ['view-post', 'View Post', 'view post'],
        ['post-post', 'Post Post', 'add post'],
        ['post-located-post', 'Post Post to any location', 'add post'],
        ['edit-post', 'Edit Post', 'edit existing post'],
        ['delete-post', 'Delete Post', 'delete existing post'],
        ['publish-post', 'Publish Post', 'publish post'],
        ['pin-post', 'Pin Post', 'pin post'],
        ['assign-post', 'Assign Post', 'assign post'],
        ['list_views-post', 'List views of Post', 'list views of post'],
        // Product
        ['list-product', 'List Products', 'list all products'],
        ['view-product', 'View Product', 'view product'],
        ['post-product', 'Post Product', 'add product'],
        ['edit-product', 'Edit Product', 'edit existing product'],
        ['delete-product', 'Delete Product', 'delete existing product'],
        ['publish-product', 'Publish Product', 'publish product'],
        // Provider
        ['list-provider', 'List service provider', 'list all service providers'],
        ['post-provider', 'Post service provider', 'add service provider'],
        ['view-provider', 'View service provider', 'view service provider'],
        ['edit-provider', 'Edit service provider', 'edit service provider'],
        ['delete-provider', 'Delete service provider', 'delete service provider'],
        ['notify-provider', 'Notify service provider', 'Notify provider, property managers and admins'],
        ['assign-provider', 'Assign district and building to provider', 'Assign district and building to provider'],
        // Request
        ['list-request', 'List ServiceRequests', 'list all ServiceRequests'],
        ['post-request_tenant', 'Tenant Post ServiceRequests', 'tenant add serviceRequest'],
        ['post-request_service', 'Service Post ServiceRequests', 'Service add serviceRequest'],
        ['post-request', 'Agent Post ServiceRequests', 'agent add serviceRequest'],
        ['edit-request', 'Agent Edit ServiceRequests', 'agent edit existing serviceRequest'],
        ['edit-request_tenant', 'Tenant Edit ServiceRequests', 'tenant edit existing serviceRequest'],
        ['edit-request_service', 'Service Edit ServiceRequests', 'Service edit existing serviceRequest'],
        ['delete-request', 'Delete ServiceRequests', 'delete existing serviceRequest'],
        ['assign-request', 'Assign property manager or admin to the request', 'Assign property manager or admin to the request'],
        // Audit
        ['list-audit', 'List Audit', 'list all audits'],
        // Building
        ['list-building', 'List Buildings', 'list Buildings'],
        ['view-building', 'View Building', 'view buildings'],
        ['post-building', 'Post Building', 'add buildings'],
        ['edit-building', 'Edit Building', 'edit existing buildings'],
        ['delete-building', 'Delete Building', 'delete existing building'],
        ['assign-building', 'Assign managers to building', 'Assign managers to building'],
        // Address
        ['list-address', 'List Address', 'list address'],
        ['view-address', 'View Address', 'view address'],
        ['post-address', 'Post Address', 'add address'],
        ['edit-address', 'Edit Address', 'edit existing address'],
        ['delete-address', 'Delete Address', 'delete existing address'],
        // Unit
        ['list-unit', 'List unit', 'list unit'],
        ['view-unit', 'View unit', 'view unit'],
        ['post-unit', 'Post unit', 'add unit'],
        ['edit-unit', 'Edit unit', 'edit existing unit'],
        ['delete-unit', 'Delete unit', 'delete existing unit'],
        // Property manager
        ['list-property_manager', 'List property manager', 'list property manager'],
        ['view-property_manager', 'View property manager', 'view property manager'],
        ['post-property_manager', 'Post property manager', 'add property manager'],
        ['edit-property_manager', 'Edit property manager', 'edit existing property manager'],
        ['delete-property_manager', 'Delete property manager', 'delete existing property manager'],
        ['assign-property_manager', 'Assign district and building to property manager', 'Assign district and building to property manager'],
        // District
        ['list-district', 'List district', 'list district'],
        ['view-district', 'View district', 'view district'],
        ['post-district', 'Post district', 'add district'],
        ['edit-district', 'Edit district', 'edit existing district'],
        ['delete-district', 'Delete district', 'delete existing district'],
        // Real estate
        ['view-real_estate', 'View real estate', 'view real estate'],
        ['edit-real_estate', 'Edit real estate', 'edit existing real estate'],
        // Template
        ['list-template', 'List template', 'list template'],
        ['view-template', 'View template', 'view template'],
        ['post-template', 'Post template', 'add template'],
        ['edit-template', 'Edit template', 'edit existing template'],
        ['delete-template', 'Delete template', 'delete existing template'],
        // Service request categories
        ['list-service_request_category', 'List service_request_category', 'list service_request_category'],
        ['view-service_request_category', 'View service_request_category', 'view service_request_category'],
        ['post-service_request_category', 'Post service_request_category', 'add service_request_category'],
        ['edit-service_request_category', 'Edit service_request_category', 'edit existing service_request_category'],
        ['delete-service_request_category', 'Delete service_request_category', 'delete existing service_request_category'],
        // Cleanify requests
        ['list-cleanify_request', 'List cleanify requests', 'list cleanify requests'],
        ['post-cleanify_request', 'Post cleanify requests', 'add cleanify requests'],
    ];
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (self::ps as $p) {
            (new Permission([
                'name' => $p[0],
                'display_name' => $p[1],
                'description' => $p[2],
            ]))->save();
        }
    }
}
