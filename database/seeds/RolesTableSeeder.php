<?php

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userPermissions = Permission::where('name', 'like', '%-user')->get();
        $postPermissions = Permission::where('name', 'like', '%-post')->get();
        $prodPermissions = Permission::where('name', 'like', '%-product')->get();
        $reqPermissions = Permission::where('name', 'like', '%-request')->get();
        $providerPermissions = Permission::where('name', 'like', '%-provider')->get();
        $auditPermissions = Permission::where('name', 'like', '%-audit')->get();
        $commentPermissions = Permission::where('name', 'like', '%-comment')->get();
        $tenantPermissions = Permission::where('name', 'like', '%-tenant')->get();
        $buildingPermissions = Permission::where('name', 'like', '%-building')->get();
        $addressPermissions = Permission::where('name', 'like', '%-address')->get();
        $unitPermissions = Permission::where('name', 'like', '%-unit')->get();
        $pManagerPermissions = Permission::where('name', 'like', '%-property_manager')->get();
        $districtPermissions = Permission::where('name', 'like', '%-district')->get();
        $realEstatePermissions = Permission::where('name', 'like', '%-real_estate')->get();
        $templatePermissions = Permission::where('name', 'like', '%-template')->get();
        $sReqCategPermissions = Permission::where('name', 'like', '%-service_request_category')->get();
        $cleanifyPermissions = Permission::where('name', 'like', '%-cleanify_request')->get();

        $allPermissions = $userPermissions
            ->merge($postPermissions)
            ->merge($prodPermissions)
            ->merge($reqPermissions)
            ->merge($providerPermissions)
            ->merge($commentPermissions)
            ->merge($tenantPermissions)
            ->merge($buildingPermissions)
            ->merge($addressPermissions)
            ->merge($unitPermissions)
            ->merge($pManagerPermissions)
            ->merge($districtPermissions)
            ->merge($realEstatePermissions)
            ->merge($templatePermissions)
            ->merge($sReqCategPermissions)
            ->merge($cleanifyPermissions)
            ->merge($auditPermissions);

        $adminPermissions = $userPermissions
            ->merge($providerPermissions)
            ->merge($commentPermissions)
            ->merge($tenantPermissions)
            ->merge($buildingPermissions)
            ->merge($addressPermissions)
            ->merge($unitPermissions)
            ->merge($pManagerPermissions)
            ->merge($districtPermissions)
            ->merge($realEstatePermissions)
            ->merge($templatePermissions)
            ->merge($sReqCategPermissions)
            ->merge($reqPermissions);

        $superAdmin = new Role();
        $superAdmin->name = 'super_admin';
        $superAdmin->display_name = 'Super admin';
        $superAdmin->description = '';
        $superAdmin->save();
        foreach ($allPermissions as $permission) {
            $superAdmin->attachPermission($permission);
        }

        $RLCAdmin = new Role();
        $RLCAdmin->name = 'administrator';
        $RLCAdmin->display_name = 'Real estate company';
        $RLCAdmin->description = '';
        $RLCAdmin->save();
        foreach ($adminPermissions as $permission) {
            $RLCAdmin->attachPermission($permission);
        }

        $RLCManager = new Role();
        $RLCManager->name = 'manager';
        $RLCManager->display_name = 'Real Estate Employee';
        $RLCManager->description = '';
        $RLCManager->save();
        foreach ($adminPermissions as $permission) {
            $RLCManager->attachPermission($permission);
        }

        $RLCHomeowner = new Role();
        $RLCHomeowner->name = 'homeowner';
        $RLCHomeowner->display_name = 'RLC Homeowner';
        $RLCHomeowner->description = '';
        $RLCHomeowner->save();

        $RLCService = new Role();
        $RLCService->name = 'service';
        $RLCService->display_name = 'RLC Third part Service';
        $RLCService->description = '';
        $RLCService->save();
        $servicePerms = [
            'list-request',
            'post-comment',
            'list-service_request_category',
            'post-request_service',
            'edit-request_service'
        ];
        foreach ($servicePerms as $p) {
            $RLCService->attachPermission(Permission::where('name', $p)->first());
        }

        $RLCUser = new Role();
        $RLCUser->name = 'registered';
        $RLCUser->display_name = 'Users (tenants)';
        $RLCUser->description = '';
        $RLCUser->save();
        $tenantPerms = [
            'post-post', 'post-product', 'post-comment',
            'list-service_request_category', 'view-service_request_category',
            'post-request_tenant', 'edit-request_tenant', 'list-request',
            'post-cleanify_request', 'view-real_estate'
        ];
        foreach ($tenantPerms as $p) {
            $RLCUser->attachPermission(Permission::where('name', $p)->first());
        }

        $reqTenantPermissions = Permission::where('name', 'like', '%-request-tenant')->get();
        foreach ($reqTenantPermissions as $permission) {
            $RLCUser->attachPermission($permission);
        }
    }
}
