<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\InternalNotice;
use App\Repositories\InternalNoticeRepository;

trait MakeInternalNoticeTrait
{
    /**
     * Create fake instance of InternalNotice and save it in database
     *
     * @param array $internalNoticeFields
     * @return InternalNotice
     */
    public function makeInternalNotice($internalNoticeFields = [])
    {
        /** @var InternalNoticeRepository $internalNoticeRepo */
        $internalNoticeRepo = \App::make(InternalNoticeRepository::class);
        $theme = $this->fakeInternalNoticeData($internalNoticeFields);
        return $internalNoticeRepo->create($theme);
    }

    /**
     * Get fake instance of InternalNotice
     *
     * @param array $internalNoticeFields
     * @return InternalNotice
     */
    public function fakeInternalNotice($internalNoticeFields = [])
    {
        return new InternalNotice($this->fakeInternalNoticeData($internalNoticeFields));
    }

    /**
     * Get fake data of InternalNotice
     *
     * @param array $internalNoticeFields
     * @return array
     */
    public function fakeInternalNoticeData($internalNoticeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'request_id' => $fake->randomDigitNotNull,
            'user_id' => $fake->randomDigitNotNull,
            'comment' => $fake->text,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'updated_at' => $fake->date('Y-m-d H:i:s')
        ], $internalNoticeFields);
    }
}
