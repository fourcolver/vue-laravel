<?php

use App\Models\TemplateCategory;
use Illuminate\Database\Seeder;

/**
 * Class TemplateCategoriesTableSeeder
 */
class TemplateCategoriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->createParentCategories();
        $this->createUserCategories();
        $this->createTenantCategories();
        $this->createPostCategories();
        $this->createProductCategories();
        $this->createRequestCategories();
        $this->createManagerCategories();
        $this->createServiceProviderCategories();
        $this->createCleanifyRequestCategories();
    }

    private function createParentCategories()
    {
        $templates = [
            [
                'parent_id' => null,
                'name' => 'user',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'tenant',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'post',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'product',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'request',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'manager',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'service_provider',
                'description' => '',
            ],
            [
                'parent_id' => null,
                'name' => 'cleanify_request',
                'description' => '',
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createUserCategories()
    {
        $templates = [
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'new_admin',
                'description' => 'User create admin notification',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',

                    'subjectName' => 'subject.name',
                    'subjectEmail' => 'subject.email',
                    'subjectPhone' => 'subject.phone',
                    'subjectTitle' => 'constant.subject.title',
                ],
                'subject' => 'New admin created',
                'body' => <<<HTML
<p>Hello {{name}}</p>
<p>A new admin account was created:</p>
<p>User {{subjectName}}</p>
<p>Email {{subjectEmail}}</p>
HTML
            ],
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'reset_password',
                'description' => 'User reset password email',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',
                    'passwordResetUrl' => 'passwordResetUrl',
                ],
                'subject' => 'Password reset request for your account',
                'body' => <<<HTML
<p>Hello {{title}} {{name}}</p>
<p>You are receiving this email because we received a password reset request for your account.</p>
<p>Reset Password {{passwordResetUrl}}</p><p>If you did not request a password reset, no further action is required.</p>
HTML
            ],
            [
                'parent_id' => 1,
                'system' => 1,
                'name' => 'reset_password_success',
                'description' => 'Password changed successfully',
                'tag_map' => [
                    'name' => 'user.name',
                    'email' => 'user.email',
                    'phone' => 'user.phone',
                    'title' => 'constant.user.title',
                ],
                'subject' => 'Password changed successfully',
                'body' => <<<HTML
<p>You changed your password successfully.</p>
<p>If you did change password, no further action is required.</p>
<p>If you did not change password, protect your account.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createTenantCategories()
    {
        $templates = [
            [
                'parent_id' => 2,
                'system' => 1,
                'name' => 'tenant_credentials',
                'description' => 'Email sent to tenant, containing the PDF with the credentials and tenancy details.',
                'tag_map' => [
                    'name' => 'user.name',
                    'birthDate' => 'tenant.birthDate',
                    'mobilePhone' => 'tenant.mobile_phone',
                    'privatePhone' => 'tenant.private_phone',
                    'email' => 'tenant.email',
                    'phone' => 'tenant.phone',
                    'title' => 'constant.tenant.title',
                    'tenantCredentials' => 'tenantCredentials',
                ],
                'subject' => 'Account created',
                'body' => <<<HTML
<p>Hello {{title}} {{name}}</p>
<p>Your account was created.</p>
<p>Here is an pdf with credentials.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createPostCategories()
    {
        $templates = [
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'pinned_post',
                'description' => 'Email sent to tenants when admin publishes a pinned post',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'title' => 'post.title',
                    'content' => 'post.content',
                    'execution_start' => 'post.executionStartStr',
                    'execution_end' => 'post.executionEndStr',
                    'category' => 'post.categoryStr',
                    'providers' => 'post.providersStr',
                    'buildings' => 'post.buildingsStr',
                    'districts' => 'post.districtsStr',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New Pined post: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Title {{title}}.</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the announcement.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'post_published',
                'description' => 'Email sent to neighbour tenants when admin publishes a post, or the post is automatically published',
                'tag_map' => [
                    'authorSalutation' => 'post.user.title',
                    'authorName' => 'post.user.name',
                    'salutation' => 'receiver.title',
                    'name' => 'receiver.name',
                    'content' => 'post.content',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'New post published {{authorSalutation}} {{authorName}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>{{authorSalutation}} {{authorName}} published a new post.</p>
<p><em>{{content}}</em></p>
<p>Use <a href="{{autologin}}">this link</a> to view the published post.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'new_post',
                'description' => 'Email sent to admins when tenant creates a new post',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'content' => 'post.content',
                    'type' => 'post.type',
                    'autologin' => 'user.autologinUrl',
                ],
                'subject' => 'New tenant post',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Tenant {{subjectSalutation}} {{subjectName}} added a new post</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the published post.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'post_liked',
                'description' => 'Email sent to post author when tenant liked the post',
                'tag_map' => [
                    'salutation' => 'post.user.title',
                    'name' => 'post.user.name',
                    'likerSalutation' => 'user.title',
                    'likerName' => 'user.name',
                    'content' => 'post.content',
                    'autologin' => 'post.user.autologinUrl',
                ],
                'subject' => '{{likerSalutation}} {{likerName}} liked your post',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Tenant {{likerSalutation}} {{likerName}} liked your post:</p>
<p>{{content}}.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the liked post.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'post_commented',
                'description' => 'Email sent to post author when tenant comments on the post',
                'tag_map' => [
                    'salutation' => 'post.user.title',
                    'name' => 'post.user.name',
                    'commenterSalutation' => 'user.title',
                    'commenterName' => 'user.name',
                    'content' => 'post.content',
                    'comment' => 'comment.comment',
                    'autologin' => 'post.user.autologinUrl',
                ],
                'subject' => '{{commenterSalutation}} {{commenterName}} commented on your post',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Tenant {{commenterSalutation}} {{commenterName}} commented on your post:</p>
<p><em>{{comment}}</em>.</p>
<p>Use <a href="{{autologin}}">this link</a> to view the post.</p>
HTML
            ],
            [
                'parent_id' => 3,
                'system' => 1,
                'name' => 'post_new_tenant_in_neighbour',
                'description' => 'Email sent to neighbour tenants when new neighbour moves in the neighbourhood',
                'tag_map' => [
                    'subjectSalutation' => 'post.user.title',
                    'subjectName' => 'post.user.name',
                    'salutation' => 'receiver.title',
                    'name' => 'receiver.name',
                    'content' => 'post.content',
                    'autologin' => 'receiver.autologinUrl',
                ],
                'subject' => 'New tenant in the neighbour',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>You got a new neighbour: {{subjectSalutation}} {{subjectName}}.</p>
<p><em>{{content}}</em></p>
<p>Use <a href="{{autologin}}">this link</a> to view the post.</p>
HTML
            ],
        ];
        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createProductCategories()
    {
        $templates = [
            [
                'parent_id' => 4,
                'name' => 'new_product',
                'description' => 'Email sent to admins when tenant creates a new product',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'authorSalutation' => 'product.user.title',
                    'authorName' => 'product.user.name',
                    'title' => 'product.title',
                    'type' => 'product.type',
                ],
                'subject' => 'New tenant product',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Tenant {{subjectSalutation}} {{subjectName}} added a new product</p>
<p>{{title}}.</p>
HTML
            ],
            [
                'parent_id' => 4,
                'name' => 'product_liked',
                'description' => 'Email sent to product author when tenant liked the product in marketplace',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'authorSalutation' => 'product.user.title',
                    'authorName' => 'product.user.name',
                    'title' => 'product.title',
                    'type' => 'product.type',
                ],
                'subject' => '{{salutation}} {{name}} liked your post',
                'body' => <<<HTML
<p>Hello {{authorSalutation}} {{authorName}},</p>
<p>Tenant {{salutation}} {{name}} liked your product:</p>
<p>{{title}}.</p>
HTML
            ],
            [
                'parent_id' => 4,
                'name' => 'product_commented',
                'description' => 'Email sent to product author when tenant comments on the product',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'authorSalutation' => 'product.user.title',
                    'authorName' => 'product.user.name',
                    'title' => 'product.title',
                    'type' => 'product.type',
                    'comment' => 'comment.comment',
                ],
                'subject' => '{{salutation}} {{name}} commented on your post',
                'body' => <<<HTML
<p>Hello {{authorSalutation}} {{authorName}},</p>
<p>Tenant {{salutation}} {{name}} commented on  your product:</p>
<p><em>{{title}}</em>.</p>
<p>Comment:</p>
<p><em>{{comment}}</em>.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createRequestCategories()
    {
        $templates = [
            [
                'parent_id' => 5,
                'system' => 0,
                'type' => TemplateCategory::TypeCommunication,
                'name' => 'communication_tenant',
                'description' => 'Request Tenant communication templates',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                ],
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}',
                'body' => '',
            ],
            [
                'parent_id' => 5,
                'system' => 0,
                'type' => TemplateCategory::TypeCommunication,
                'name' => 'communication_service_chat',
                'description' => 'Request Service providers communication templates',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                ],
                'subject' => 'Hello {{subjectSalutation}} {{subjectName}}',
                'body' => '',
            ],
            [
                'parent_id' => 5,
                'system' => 1,
                'name' => 'new_request',
                'description' => 'Email sent to property managers when tenant creates a new request.',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'unit' => 'request.unit.name',
                    'building' => 'request.unit.building.name',
                ],
                'subject' => 'New Tenant request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Tenant {{subjectSalutation}} {{subjectName}} created a new request</p>
<p>Unit: {{category}}.</p>
<p>Category: {{category}}.</p>
<p>Title: {{title}}.</p>
<p>{{description}}.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'communication_service_email',
                'system' => 0,
                'description' => 'Notify service provider -> sends email to service provider and others (BCC, CC).',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                ],
                'subject' => 'New assignment to request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>You have been assigned to an new request {{title}</p>
<p>Category: {{category}}.</p>
<p>Title: {{title}}.</p>
<p>{{description}}.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_comment',
                'description' => 'When any party adds a new comment (tenant, property manager, service partner, admin or super admin) we notify all assigned people',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'commenterSalutation' => 'comment.user.title',
                    'commenterName' => 'comment.user.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'comment' => 'comment.comment'
                ],
                'subject' => 'New comment for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>A new comment by {{commenterSalutation}} {{commenterName}} was made for request: {{title}}</p>
<p><em>{{comment}}</em>.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_upload',
                'description' => 'When any party uploads a document/image',
                'tag_map' => [
                    'receiverSalutation' => 'receiver.title',
                    'receiverName' => 'receiver.name',
                    'uploaderSalutation' => 'uploader.title',
                    'uploaderName' => 'uploader.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                ],
                'subject' => '{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{receiverSalutation}} {{receiverName}},</p>
<p>{{uploaderSalutation}} {{uploaderName}} uploaded a new document for request: {{title}}.</p>
<p>Please find the uploaded file in the attachments of this email.</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_admin_change_status',
                'description' => 'When the Property Manager, Admin or Service partner change the status we notify the tenant, each time when status is changed from X to XY',
                'tag_map' => [
                    'salutation' => 'request.tenant.title',
                    'name' => 'request.tenant.user.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'status' => 'constant.request.status',
                    'originalStatus' => 'constant.originalRequest.status',
                ],
                'subject' => 'Status changed for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Admin changed status for request {{title}} from {{originalStatus}} to {{status}}</p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_internal_comment',
                'description' => 'When admin or service partner add a internal comment, we will notify each other.',
                'tag_map' => [
                    'receiverSalutation' => 'receiver.title',
                    'receiverName' => 'receiver.name',
                    'senderSalutation' => 'sender.title',
                    'senderName' => 'sender.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'category' => 'request.category.name',
                    'comment' => 'comment.comment'
                ],
                'subject' => 'New internal comment for request: {{title}}',
                'body' => <<<HTML
<p>Hello {{receiverSalutation}} {{receiverName}},</p>
<p>{{senderSalutation}} {{senderName}} added a new private comment for request: {{title}}</p>
<p><em>{{comment}}.</em></p>
HTML
            ],
            [
                'parent_id' => 5,
                'name' => 'request_due_date_reminder',
                'description' => 'Send reminder email to property manager / admin 1 day before the due date is reached',
                'tag_map' => [
                    'salutation' => 'user.title',
                    'name' => 'user.name',
                    'subjectSalutation' => 'subject.title',
                    'subjectName' => 'subject.name',
                    'title' => 'request.title',
                    'description' => 'request.description',
                    'dueDate' => 'request.due_date',
                    'category' => 'request.category.name',
                ],
                'subject' => 'Request: {{title}} is approaching its due date',
                'body' => <<<HTML
<p>Hello {{salutation}} {{name}},</p>
<p>Due date for request {{title}} is {{dueDate}}</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createManagerCategories()
    {
        $templates = [

        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createServiceProviderCategories()
    {
        $templates = [

        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }

    private function createCleanifyRequestCategories()
    {
        $templates = [
            [
                'parent_id' => 8,
                'name' => 'cleanify_request_email',
                'description' => 'Email sent to Cleanify when the tenant makes a request.',
                'tag_map' => [
                    'salutation' => 'form.title',
                    'firstName' => 'form.first_name',
                    'lastName' => 'form.last_name',
                    'address' => 'form.address',
                    'zip' => 'form.zip',
                    'city' => 'form.city',
                    'email' => 'form.email',
                    'phone' => 'form.phone',
                ],
                'subject' => 'New Cleanify request from: {{salutation}} {{firstName}} {{lastName}}',
                'body' => <<<HTML
<p>New Cleanify request,</p>
<p>Name : {{salutation}} {{firstName}} {{lastName}}.</p>
<p>Phone : {{phone}}.</p>
<p>Email : {{email}}.</p>
<p>Address:</p>
<p>{{address}}, {{city}} {{zip}}.</p>
HTML
            ],
        ];

        foreach ($templates as $template) {
            (new TemplateCategory())->create($template);
        }
    }
}
