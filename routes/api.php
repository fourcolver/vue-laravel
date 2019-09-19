<?php

Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', 'AuthController@login')->name('login');
    Route::post('autologin', 'AuthController@autologin');

    Route::post('forgot_password', 'PasswordResetController@forgot');
    Route::get('forgot_password/{token}', 'PasswordResetController@find');
    Route::post('reset_password', 'PasswordResetController@reset');

    // private routes
    Route::middleware('auth:api')->group(function () {
        Route::get('/logout', 'AuthController@logout')->name('logout');
        // Route::get('/me', 'AuthController@user')->name('me');
    });
});

// Constants
Route::get('/constants', 'UtilsAPIController@constants')->name('constants');
Route::put('/tenants/resetpassword', 'TenantAPIController@resetPassword');

// private routes
Route::middleware('auth:api', 'throttle:180,1', 'locale')->group(function () {
    // Users
    Route::get('/users', 'UserAPIController@index')->name('users');
    Route::get('/users/me', 'UserAPIController@showLoggedIn')->name('users.me');
    Route::get('/users/requestManagers', 'UserAPIController@requestManagers')->name('users.requestManagers');
    Route::get('/users/{id}', 'UserAPIController@show')->name('users.show');

    Route::post('/users', 'UserAPIController@store')->name('users.store');
    Route::post('/users/me/upload_image', 'UserAPIController@uploadImageLoggedIn')->name('users.me.upload.image');
    Route::post('/users/{id}/upload_image', 'UserAPIController@uploadImage')->name('users.upload.image');

    Route::put('/users/me', 'UserAPIController@updateLoggedIn')->name('users.me.update');
    Route::put('/users/me/changePassword', 'UserAPIController@changePassword')->name('users.me.changePassword');
    Route::put('/users/me/settings', 'UserSettingsAPIController@updateLoggedIn')->name('users.me.settings.update');

    Route::put('/users/{id}/settings', 'UserSettingsAPIController@update')->name('users.settings.update');
    Route::put('/users/{id}', 'UserAPIController@update')->name('users.update');

    Route::delete('/users/{id}', 'UserAPIController@destroy')->name('users.destroy');

    // Tenants
    Route::get('/tenants', 'TenantAPIController@index')->name('tenants');
    Route::get('/tenants/gender-statistics', 'StatisticsAPIController@tenantsGenderStatistics')->name('tenants.gender-statistics');
    Route::get('/tenants/latest', 'TenantAPIController@latest')->name('tenants.latest');
    Route::get('/tenants/me', 'TenantAPIController@showLoggedIn')->name('tenants.me');
    Route::get('/tenants/{id}', 'TenantAPIController@show')->name('tenants.show');
    Route::get('/tenants/{id}/statistics', 'StatisticsAPIController@tenantStatistics')->name('tenants.statistics.show');

    Route::post('/tenants', 'TenantAPIController@store')->name('tenants.store');
    Route::post('/addReview', 'TenantAPIController@addReview');
    Route::post('/tenants/{id}/media', 'MediaAPIController@tenantUpload')->name('tenants.media.upload');
    Route::post('/tenants/{id}/send-credentials', 'TenantAPIController@sendCredentials');
    Route::post('/tenants/{id}/download-credentials', 'TenantAPIController@downloadCredentials');

    Route::put('/tenants/me', 'TenantAPIController@updateLoggedIn')->name('tenants.me.update');
    Route::put('/tenants/{id}', 'TenantAPIController@update')->name('tenants.update');
    Route::put('/tenants/{id}/status', 'TenantAPIController@changeStatus')->name('tenants.changeStatus');

    Route::delete('/tenants/{id}', 'TenantAPIController@destroy')->name('tenants.destroy');
    Route::delete('/tenants/{id}/media/{media_id}', 'MediaAPIController@tenantDestroy')->name('tenants.media.destroy');

    // Location
    Route::get('/states', 'StateAPIController@index')->name('states');

    Route::get('/addresses', 'AddressAPIController@index')->name('addresses');
    Route::get('/addresses/{id}', 'AddressAPIController@show')->name('addresses.show');

    Route::post('/addresses', 'AddressAPIController@store')->name('addresses.store');

    Route::put('/addresses/{id}', 'AddressAPIController@update')->name('addresses.update');

    Route::delete('/addresses/{id}', 'AddressAPIController@destroy')->name('addresses.destroy');

    // Buildings
    Route::get('/buildings', 'BuildingAPIController@index')->name('buildings');
    Route::get('/buildings/latest', 'BuildingAPIController@latest')->name('buildings.latest');
    Route::get('/buildings/map', 'BuildingAPIController@map')->name('buildings.map');
    Route::get('/buildings/statistics', 'StatisticsAPIController@allBuildingStatistics')->name('buildings.statistics.all');
    Route::get('/buildings/{id}', 'BuildingAPIController@show')->name('buildings.show');
    Route::get('/buildings/{id}/statistics', 'StatisticsAPIController@buildingStatistics')->name('buildings.statistics.show');

    Route::post('/buildings', 'BuildingAPIController@store')->name('buildings.store');
    Route::post('/buildings/{id}/media', 'MediaAPIController@buildingUpload')->name('buildings.media.upload');
    Route::post('/buildings/{id}/propertyManagers', 'BuildingAPIController@assignManagers')->name('buildings.assign.managers');
    Route::post('/buildings/deletewithids', 'BuildingAPIController@destroyWithIds')->name('buildings.destroyWithIds');
    Route::post('/buildings/checkunitrequest', 'BuildingAPIController@checkUnitRequest')->name('buildings.checkUnitRequest');

    Route::put('/buildings/{id}', 'BuildingAPIController@update')->name('buildings.update');

    Route::delete('/buildings/{id}', 'BuildingAPIController@destroy')->name('buildings.destroy');
    Route::delete('/buildings/{building_id}/media/{media_id}', 'MediaAPIController@buildingDestroy')->name('buildings.media.destroy');
    Route::delete('/buildings/{building_id}/service/{service_id}', 'BuildingAPIController@serviceRemove')->name('buildings.service.destroy');
    Route::delete('/buildings/{building_id}/propertyManagers/{manager_id}', 'BuildingAPIController@unAssignPropertyManager')->name('buildings.manager.destroy');
    // Units
    Route::get('/units', 'UnitAPIController@index')->name('units');
    Route::get('/units/{id}', 'UnitAPIController@show')->name('units.show');

    Route::post('/units', 'UnitAPIController@store')->name('units.store');

    Route::put('/units/{id}', 'UnitAPIController@update')->name('units.update');

    Route::delete('/units/{id}', 'UnitAPIController@destroy')->name('units.destroy');

    // Real Estate
    Route::get('/realEstate', 'RealEstateAPIController@show')->name('units.show');
    Route::put('/realEstate', 'RealEstateAPIController@update')->name('units.update');

    // Services
    Route::get('/services', 'ServiceProviderAPIController@index')->name('services');
    Route::get('/services/category', 'ServiceProviderAPIController@indexByCategory')->name('services.byCategory');
    Route::get('/services/{id}', 'ServiceProviderAPIController@show')->name('services.show');
    Route::post('/services', 'ServiceProviderAPIController@store')->name('services.store');
    Route::put('/services/{id}', 'ServiceProviderAPIController@update')->name('services.update');
    Route::delete('/services/{id}', 'ServiceProviderAPIController@destroy')->name('services.destroy');
    Route::post('/services/{id}/districts/{district_id}', 'ServiceProviderAPIController@assignDistrict');
    Route::delete('/services/{id}/districts/{district_id}', 'ServiceProviderAPIController@unassignDistrict');
    Route::post('/services/{id}/buildings/{building_id}', 'ServiceProviderAPIController@assignBuilding');
    Route::delete('/services/{id}/buildings/{building_id}', 'ServiceProviderAPIController@unassignBuilding');
    Route::get('/services/{id}/locations', 'ServiceProviderAPIController@getLocations');

    // Districts
    Route::get('/districts', 'DistrictAPIController@index')->name('services');
    Route::get('/districts/{id}', 'DistrictAPIController@show')->name('services.show');
    Route::post('/districts', 'DistrictAPIController@store')->name('services.store');
    Route::put('/districts/{id}', 'DistrictAPIController@update')->name('services.update');
    Route::delete('/districts/{id}', 'DistrictAPIController@destroy')->name('services.destroy');

    // Posts
    Route::resource('posts', 'PostAPIController');
    Route::post('posts/{id}/publish', 'PostAPIController@publish')->name('posts.publish');
    Route::post('posts/{id}/like', 'PostAPIController@like')->name('posts.like');
    Route::post('posts/{id}/unlike', 'PostAPIController@unlike')->name('posts.unlike');
    Route::post('posts/{id}/media', 'MediaAPIController@postUpload')->name('posts.media.upload');
    Route::delete('posts/{id}/media/{media_id}', 'MediaAPIController@postDestroy')->name('posts.media.destroy');
    Route::post('posts/{id}/comments', 'CommentAPIController@storePostComment')->name('posts.store.comment');
    Route::get('/posts/{id}/locations', 'PostAPIController@getLocations');
    Route::post('/posts/{id}/buildings/{building_id}', 'PostAPIController@assignBuilding');
    Route::delete('/posts/{id}/buildings/{building_id}', 'PostAPIController@unassignBuilding');
    Route::post('/posts/{id}/districts/{district_id}', 'PostAPIController@assignDistrict');
    Route::delete('/posts/{id}/districts/{district_id}', 'PostAPIController@unassignDistrict');
    Route::post('/posts/{id}/providers/{provider_id}', 'PostAPIController@assignProvider');
    Route::delete('/posts/{id}/providers/{provider_id}', 'PostAPIController@unassignProvider');
    Route::put('/posts/{id}/views', 'PostAPIController@incrementViews');
    Route::get('/posts/{id}/views', 'PostAPIController@indexViews');

    // News
    Route::get('news/rss.xml', 'NewsAPIController@showNewsRSS');
    Route::get('news/weather.json', 'NewsAPIController@showWeatherJSON');

    //Internal Notices
    Route::resource('internalNotices', 'InternalNoticeAPIController');

    // Comments & Notifications & Conversations
    Route::get('/comments', 'CommentAPIController@index')->name('comments');
    Route::get('/comments/{id}', 'CommentAPIController@children')->name('comments.children');
    Route::put('/comments/{id}', 'CommentAPIController@updateComment')->name('comments.update');
    Route::delete('/comments/{id}', 'CommentAPIController@destroyComment')->name('comments.destroy');
    Route::get('/notifications', 'NotificationAPIController@index')->name('notifications');
    Route::post('/notifications', 'NotificationAPIController@markAllAsRead')->name('notifications.markAll');
    Route::post('/notifications/{id}', 'NotificationAPIController@markAsRead')->name('notifications.mark');
    Route::get('/conversations', 'ConversationAPIController@show');
    Route::post('/conversations/{id}/comments', 'ConversationAPIController@storeComment');

    Route::get('cleanify', 'CleanifyRequestAPIController@index');
    Route::post('cleanify', 'CleanifyRequestAPIController@store');
    // Service Requests Category
    Route::get('/requestCategories', 'ServiceRequestCategoryAPIController@index')->name('requests.categories');
    Route::get('/requestCategories/tree', 'ServiceRequestCategoryAPIController@categoryTree')->name('requests.categories.tree');
    Route::get('/requestCategories/{id}', 'ServiceRequestCategoryAPIController@show')->name('requests.categories.show');
    Route::post('/requestCategories', 'ServiceRequestCategoryAPIController@store')->name('requests.categories.store');
    Route::put('/requestCategories/{id}', 'ServiceRequestCategoryAPIController@update')->name('requests.categories.update');
    Route::delete('/requestCategories/{id}', 'ServiceRequestCategoryAPIController@destroy')->name('requests.categories.destroy');

    // Service Requests
    Route::get('/requests', 'ServiceRequestAPIController@index')->name('requests');
    Route::get('/requests/statistics', 'StatisticsAPIController@requestsStatistics')->name('requests.statistics');
    Route::get('/requests/{id}', 'ServiceRequestAPIController@show')->name('requests.show');
    Route::post('/requests', 'ServiceRequestAPIController@store')->name('requests.store');
    Route::post('/requests/{id}/media', 'MediaAPIController@serviceRequestUpload')->name('requests.media.upload');
    Route::post('/requests/{id}/comments', 'CommentAPIController@storeRequestComment')->name('requests.comment.store');
    Route::post('/requests/{id}/notify', 'ServiceRequestAPIController@notifyProvider')->name('requests.notify');
    Route::put('/requests/{id}', 'ServiceRequestAPIController@update')->name('requests.update');
    Route::put('/requests/{id}/status', 'ServiceRequestAPIController@changeStatus')->name('requests.changeStatus');
    Route::put('/requests/{id}/priority', 'ServiceRequestAPIController@changePriority')->name('requests.changePriority');
    Route::delete('/requests/{id}', 'ServiceRequestAPIController@destroy')->name('requests.destroy');
    Route::delete('/requests/{id}/media/{media_id}', 'MediaAPIController@serviceRequestDestroy')->name('requests.media.destroy');
    Route::get('/requests/{id}/assignees', 'ServiceRequestAPIController@getAssignees');
    Route::post('/requests/{id}/assignees/{assignee_id}', 'ServiceRequestAPIController@assignUser');
    Route::delete('/requests/{id}/assignees/{assignee_id}', 'ServiceRequestAPIController@unassignUser');
    Route::post('/requests/{id}/providers/{provider_id}', 'ServiceRequestAPIController@assignProvider');
    Route::delete('/requests/{id}/providers/{provider_id}', 'ServiceRequestAPIController@unassignProvider');
    Route::get('/requests/{id}/communicationTemplates', 'ServiceRequestAPIController@getCommunicationTemplates');
    Route::get('/requests/{id}/serviceCommunicationTemplates', 'ServiceRequestAPIController@getServiceCommunicationTemplates');

    // Products
    Route::resource('products', 'ProductAPIController');
    Route::post('products/{id}/like', 'ProductAPIController@like')->name('products.like');
    Route::post('products/{id}/unlike', 'ProductAPIController@unlike')->name('products.unlike');
    Route::post('products/{id}/media', 'MediaAPIController@productUpload')->name('products.media.upload');
    Route::delete('products/{id}/media/{media_id}', 'MediaAPIController@productDestroy')->name('products.media.destroy');
    Route::post('products/{id}/comments', 'CommentAPIController@storeProductComment')->name('products.store.comment');
    Route::post('products/{id}/publish', 'ProductAPIController@publish')->name('products.publish');

    // Service Requests
    Route::get('propertyManagers', 'PropertyManagerAPIController@index')->name('propertyManagers');
    Route::get('propertyManagers/{id}', 'PropertyManagerAPIController@show')->name('propertyManagers.show');
    Route::get('propertyManagers/{id}/assignments', 'PropertyManagerAPIController@getAssignments');
    Route::post('propertyManagers/idsassignments', 'PropertyManagerAPIController@getIDsAssignmentsCount');
    Route::post('propertyManagers', 'PropertyManagerAPIController@store')->name('propertyManagers.store');
    Route::put('propertyManagers/{id}', 'PropertyManagerAPIController@update')->name('propertyManagers.update');
    Route::delete('/propertyManagers/batchDelete', 'PropertyManagerAPIController@batchDelete');
    Route::delete('propertyManagers/{id}', 'PropertyManagerAPIController@destroy')->name('propertyManagers.destroy');
    Route::post('/propertyManagers/{id}/districts/{district_id}', 'PropertyManagerAPIController@assignDistrict');
    Route::delete('/propertyManagers/{id}/districts/{district_id}', 'PropertyManagerAPIController@unassignDistrict');
    Route::post('/propertyManagers/{id}/buildings/{building_id}', 'PropertyManagerAPIController@assignBuilding');
    Route::delete('/propertyManagers/{id}/buildings/{building_id}', 'PropertyManagerAPIController@unassignBuilding');

    // Templates
    Route::get('/templates', 'TemplateAPIController@index')->name('templates');
    Route::get('/templates/categories', 'TemplateAPIController@categories')->name('templates.categories');
    Route::get('/templates/{id}', 'TemplateAPIController@show')->name('templates.show');
    Route::post('/templates', 'TemplateAPIController@store')->name('templates.store');
    Route::put('/templates/{id}', 'TemplateAPIController@update')->name('templates.update');
    Route::delete('/templates/{id}', 'TemplateAPIController@destroy')->name('templates.destroy');

    // Audits
    Route::get('/audits', 'AuditAPIController@index');

    // Translations
    Route::resource('translations', 'TranslationAPIController');
    Route::get('/admin/statistics', 'StatisticsAPIController@adminStats');
    Route::get('/admin/chartRequestByCreationDate', 'StatisticsAPIController@chartRequestByCreationDate');
    Route::get('/admin/chartBuildingsByCreationDate', 'StatisticsAPIController@chartBuildingsByCreationDate');
    Route::get('/admin/chartRequestByCreationDateByColumn', 'StatisticsAPIController@chartRequestByCreationDateByColumn');
    Route::get('/admin/heatRequestByCreationDate', 'StatisticsAPIController@heatRequestByCreationDate');

    Route::get('/admin/chartRequestByColumn', 'StatisticsAPIController@chartRequestByColumn');
    Route::get('/admin/chartRequestByStatus', 'StatisticsAPIController@chartRequestByStatus');
    Route::get('/admin/chartRequestByRequestStatus', 'StatisticsAPIController@chartRequestByRequestStatus');
    Route::get('/admin/chartRequestByCategory', 'StatisticsAPIController@chartRequestByCategory');
    Route::get('/admin/chartLoginDevice', 'StatisticsAPIController@chartLoginDevice');
});


