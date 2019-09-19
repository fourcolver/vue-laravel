<?php

namespace App\Repositories;

use App\Models\CleanifyRequest;
use App\Models\Comment;
use App\Models\PasswordReset;
use App\Models\Post;
use App\Models\Product;
use App\Models\RealEstate;
use App\Models\ServiceRequest;
use App\Models\Template;
use App\Models\TemplateCategory;
use App\Models\Tenant;
use App\Models\User;
use Config;
use Illuminate\Database\Eloquent\Collection;
use InfyOm\Generator\Common\BaseRepository;
use Spatie\MediaLibrary\Models\Media;

/**
 * Class TemplateRepository
 * @package App\Repositories
 */
class TemplateRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description' => 'like',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Template::class;
    }

    /**
     * @param array $attributes
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function create(array $attributes)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        return $this->parserResult($model);
    }

    /**
     * @param User $user
     * @param User $subject
     * @return array
     */
    public function getUserNewAdminTemplate(User $user, User $subject): array
    {
        $template = $this->getByCategoryName('new_admin');

        $context = [
            'user' => $user,
            'subject' => $subject,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param $templateName
     * @return mixed
     */
    public function getByCategoryName($templateName)
    {
        $template = (new Template)->with('category')->whereHas('category', function ($q) use ($templateName) {
            $q->where('name', $templateName);
        })->first();

        return $template;
    }

    /**
     * @param array $tagMap
     * @param array $context
     * @return array
     */
    public function getTags(array $tagMap, array $context): array
    {
        $tags = [];
        foreach ($tagMap as $tag => $val) {
            $tags = [];
            foreach ($tagMap as $tag => $val) {
                if (in_array($tag, ['autologinUrl', 'passwordResetUrl', 'tenantCredentials'])) {
                    $tags[$tag] = $this->getStaticTagValue($tag, $val, $context);
                    continue;
                }

                $valMap = explode('.', $val);

                if (count($valMap) > $this->maxNesting) {
                    continue;
                }

                $trString = '';
                if ($valMap[0] == 'constant') {
                    unset($valMap[0]);
                    $valMap = array_values($valMap);
                    $trString = implode('_', $valMap);
                }

                if (!isset($context[$valMap[0]])) {
                    continue;
                }

                $cContext = $context[$valMap[0]];
                unset($valMap[0]);
                $valMap = array_values($valMap);

                $val = self::getContextValue($cContext, $valMap);

                if ($trString) {
                    $val = __('common.' . $trString . '_' . $val);
                }

                $tags[$tag] = $val;
            }
        }

        return $tags;
    }

    /**
     * @param string $tag
     * @param string $val
     * @param array $context
     * @return string
     */
    private function getStaticTagValue(string $tag, string $val, array $context)
    {
        $user = $context['user'] ?? null;
        $pwReset = $context['pwReset'] ?? null;
        $tenant = $context['tenant'] ?? null;

        if ($tag == 'autologinUrl' && $user) {
            $linkText = __('See post');
            return $this->button($user->autologinUrl, $linkText);
        }

        if ($tag == 'passwordResetUrl' && $pwReset) {
            $linkHref = url(sprintf('/reset-password?email=%s&token=%s', $user->email, $pwReset->token));
            $linkText = __('Reset password');
            return $this->button($linkHref, $linkText);
        }

        if ($tag == 'tenantCredentials' && $tenant) {
            $linkHref = env('APP_URL') . \Storage::url($tenant->pdfXFileName());
            $linkText = __('Download Credentials');
            return $this->button($linkHref, $linkText);
        }

        return $val;
    }

    /**
     * @param $url
     * @param $text
     * @return string
     */
    private
    function button($url, $text)
    {
        $linkClass = 'button button-primary';
        $linkStyle = 'font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; box-sizing: border-box; border-radius: 3px; box-shadow: 0 2px 3px rgba(0, 0, 0, 0.16); color: #FFF; display: inline-block; text-decoration: none; -webkit-text-size-adjust: none; background-color: #3490DC; border-top: 10px solid #3490DC; border-right: 18px solid #3490DC; border-bottom: 10px solid #3490DC; border-left: 18px solid #3490DC;';

        return sprintf('<a class="%s" style="%s" href="%s">%s</a>', $linkClass, $linkStyle, $url, $text);
    }

    /**
     * @param $context
     * @param $field
     * @return string
     */
    private static function getContextValue($context, $field)
    {
        if (!$context) {
            return '';
        }

        if (is_array($context)) {
            return $context[$field] ?? '';
        }

        return $context->$field ?? '';
    }

    /**
     * @param Template $template
     * @param $tagMap
     * @param $lang
     * @return array
     */
    public
    function getParsedTemplateData($template, $tagMap, $lang = ''): array
    {
        if (!$template) {
            return [
                'subject' => '',
                'body' => '',
            ];
        }

        $template = self::getParsedTemplate($template, $tagMap, $lang);

        $company = (new RealEstate())->first();
        $appUrl = env('APP_URL', '');

        $companyAddress = [
            $company->address->street,
            $company->address->street_nr . ',',
            $company->address->zip,
            $company->address->city,
        ];

        return [
            'subject' => $template->subject,
            'body' => $template->body,
            'company' => $company,
            'companyLogo' => $appUrl . '/' . $company->logo,
            'companyName' => $company->name,
            'companyAddress' => implode(' ', $companyAddress),
            'linkContact' => env('APP_URL', '#'),
            'linkTermsOfUse' => env('APP_URL', '#'),
            'linkDataProtection' => env('APP_URL', '#'),
        ];
    }

    /**
     * @param Template $template
     * @param $tagMap
     * @param $lang
     *
     * @return Template
     */
    public
    function getParsedTemplate($template, $tagMap, $lang = ''): Template
    {
        if (!$template) {
            return null;
        }

        $languages = Config::get('app.locales');
        $userLanguage = in_array($lang, array_keys($languages)) ? $lang : Config::get('app.locale');

        $translations = $template->translations()->where('language', $userLanguage)->get();
        $templateFields = [
            'subject',
            'body'
        ];

        foreach ($translations as $translation) {
            foreach ($templateFields as $field) {
                if ($translation->name == $field) {
                    $template->$field = $translation->value;
                }
            }
        }

        foreach ($templateFields as $field) {
            foreach ($tagMap as $tag => $value) {
                $template->$field = str_replace('{{' . $tag . '}}', $value, $template->$field);
            }
        }

        return $template;
    }

    /**
     * @param User $user
     * @param PasswordReset|null $pwReset
     * @return array
     */
    public
    function getUserResetPasswordTemplate(User $user, PasswordReset $pwReset): array
    {
        $template = $this->getByCategoryName('reset_password');

        $context = [
            'user' => $user,
            'pwReset' => $pwReset,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param User $user
     * @return array
     */
    public
    function getUserResetPasswordSuccessTemplate(User $user): array
    {
        $template = $this->getByCategoryName('reset_password_success');

        $context = [
            'user' => $user,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param Post $post
     * @param User $user
     * @return array
     */
    public
    function getNewPostParsedTemplate(Post $post, User $user): array
    {
        $template = $this->getByCategoryName('new_post');

        $context = [
            'user' => $user,
            'post' => $post,
            'subject' => $post->user,
        ];

        $user->redirect = "/admin/posts/" . $post->id;
        $tags = $this->getTags($template->category->tag_map, $context);
        return $this->getParsedTemplateData($template, $tags);
    }

    /**
     * @param Tenant $tenant
     * @return array
     */
    public
    function getTenantCredentialsParsedTemplate(Tenant $tenant): array
    {
        $template = $this->getByCategoryName('tenant_credentials');

        $context = [
            'tenant' => $tenant,
            'user' => $tenant->user,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $tenant->user->settings->language);
    }

    /**
     * @param Post $post
     * @param User $user
     * @return array
     */
    public
    function getPinnedPostParsedTemplate(Post $post, User $user): array
    {
        $template = $this->getByCategoryName('pinned_post');

        $user->redirect = '/news';
        $context = [
            'user' => $user,
            'post' => $post,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param Post $post
     * @param User $receiver
     * @return array
     */
    public
    function getPostParsedTemplate(Post $post, User $receiver): array
    {
        $template = $this->getByCategoryName('post_published');

        $receiver->redirect = '/news';
        $context = [
            'receiver' => $receiver,
            'post' => $post,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param Post $post
     * @param User $receiver
     * @return array
     */
    public
    function getPostNewTenantInNeighbourParsedTemplate(Post $post, User $receiver): array
    {
        $template = $this->getByCategoryName('post_new_tenant_in_neighbour');

        $context = [
            'receiver' => $receiver,
            'post' => $post,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param Post $post
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public
    function getPostCommentedParsedTemplate(Post $post, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('post_commented');

        $post->user->redirect = '/news';
        $context = [
            'user' => $user,
            'post' => $post,
            'comment' => $comment,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $post->user->settings->language);
    }

    /**
     * @param Post $post
     * @param User $user
     * @return array
     */
    public
    function getPostLikedParsedTemplate(Post $post, User $user): array
    {
        $template = $this->getByCategoryName('post_liked');

        $context = [
            'user' => $user,
            'post' => $post,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $post->user->settings->language);
    }

    /**
     * @param Product $product
     * @param User $user
     * @return array
     */
    public
    function getProductLikedParsedTemplate(Product $product, User $user): array
    {
        $template = $this->getByCategoryName('product_liked');

        $context = [
            'user' => $user,
            'product' => $product,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $product->user->settings->language);
    }

    /**
     * @param Product $product
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public
    function getProductCommentedParsedTemplate(Product $product, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('product_commented');

        $context = [
            'user' => $user,
            'product' => $product,
            'comment' => $comment,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $product->user->settings->language);
    }

    /**
     * @param ServiceRequest $request
     * @param User $user
     * @param User $subject
     * @return array
     */
    public
    function getNewRequestParsedTemplate(ServiceRequest $request, User $user, User $subject): array
    {
        $template = $this->getByCategoryName('new_request');

        $context = [
            'user' => $user,
            'subject' => $subject,
            'request' => $request,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param ServiceRequest $sr
     * @param User $user
     * @param Comment $comment
     * @return array
     */
    public
    function getRequestCommentedParsedTemplate(ServiceRequest $sr, User $user, Comment $comment): array
    {
        $template = $this->getByCategoryName('request_comment');

        $context = [
            'request' => $sr,
            'comment' => $comment,
            'user' => $user,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param ServiceRequest $sr
     * @return array
     */
    public
    function getRequestDueParsedTemplate(ServiceRequest $sr, User $receiver): array
    {
        $template = $this->getByCategoryName('request_due_date_reminder');

        $context = [
            'request' => $sr,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param ServiceRequest $sr
     * @param User $uploader
     * @param User $receiver
     * @param Media $media
     * @return array
     */
    public
    function getRequestMediaParsedTemplate(
        ServiceRequest $sr,
        User $uploader,
        User $receiver,
        Media $media
    ): array
    {
        $template = $this->getByCategoryName('request_upload');

        $context = [
            'request' => $sr,
            'media' => $media,
            'uploader' => $uploader,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        $msg = $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
        $msg['media'] = $media;
        return $msg;
    }

    /**
     * @param ServiceRequest $sr
     * @param ServiceRequest $osr
     * @param User $user
     * @return array
     */
    public
    function getRequestStatusChangedParsedTemplate(ServiceRequest $sr, ServiceRequest $osr, User $user): array
    {
        $template = $this->getByCategoryName('request_admin_change_status');

        $context = [
            'request' => $sr,
            'originalRequest' => $osr,
            'user' => $user,
        ];
        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $user->settings->language);
    }

    /**
     * @param ServiceRequest $sr
     * @param Comment $comment
     * @param User $sender
     * @param User $receiver
     * @return array
     */
    public
    function getRequestInternalCommentParsedTemplate(
        ServiceRequest $sr,
        Comment $comment,
        User $sender,
        User $receiver
    ): array
    {
        $template = $this->getByCategoryName('request_internal_comment');

        $context = [
            'request' => $sr,
            'comment' => $comment,
            'sender' => $sender,
            'receiver' => $receiver,
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $receiver->settings->language);
    }

    /**
     * @param CleanifyRequest $creq
     * @return array
     */
    public
    function getCleanifyParsedTemplate(CleanifyRequest $creq): array
    {
        $template = $this->getByCategoryName('cleanify_request_email');

        $context = [
            'form' => $creq->form
        ];

        $tags = $this->getTags($template->category->tag_map, $context);

        return $this->getParsedTemplateData($template, $tags, $creq->user->settings->language);
    }

    /**
     * @param ServiceRequest $serviceReq
     * @param User $user
     * @return Collection
     */
    public function getParsedCommunicationTemplates(ServiceRequest $serviceReq, User $user): Collection
    {
        $templates = (new Template())->whereHas('category', function ($q) {
            $q->where('type', TemplateCategory::TypeCommunication)
                ->where('name', 'communication_tenant');
        })->get();

        foreach ($templates as $template) {
            $context = [
                'user' => $user,
                'subject' => $serviceReq->tenant->user,
                'request' => $serviceReq,
            ];

            $template = self::getTemplate($template, $context);
        }

        return $templates;
    }

    /**
     * @param Template $template
     * @param array $context
     *
     * @return Template
     */
    public function getTemplate(Template $template, $context): Template
    {
        $tags = $this->getTags($template->category->tag_map, $context);

        $parsedTemplate = $this->getParsedTemplate($template, $tags);

        return $parsedTemplate;
    }

    /**
     * @param ServiceRequest $serviceReq
     * @param User $user
     * @return Collection
     */
    public function getParsedServiceCommunicationTemplates(ServiceRequest $serviceReq, User $user): Collection
    {
        $templates = (new Template())->whereHas('category', function ($q) {
            $q->where('type', TemplateCategory::TypeCommunication)
                ->where('name', 'service_communication');
        })->get();

        foreach ($templates as $template) {
            $context = [
                'user' => $user,
                'subject' => $serviceReq->tenant->user,
                'request' => $serviceReq,
            ];
            $template = self::getTemplate($template, $context);
        }

        return $templates;
    }
}
