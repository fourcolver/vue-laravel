<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\NewAdmin;
use App\Repositories\TemplateRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class NewAdminNotification
 * @package App\Jobs
 */
class NewAdminNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var User
     */
    private $user;

    /**
     * NewAdminNotification constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle()
    {
        $admins = User::where('id', '!=', $this->user->id)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'super_admin');
                $query->orWhere('name', 'administrator');
            })->get();

        $templateRepo = app()->make(TemplateRepository::class);

        foreach ($admins as $admin) {
            $admin->notify(
                new NewAdmin($admin, $this->user)
            );
        }
    }
}
