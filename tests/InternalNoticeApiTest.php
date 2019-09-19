<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeInternalNoticeTrait;
use Tests\ApiTestTrait;

class InternalNoticeApiTest extends TestCase
{
    use MakeInternalNoticeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_internal_notice()
    {
        $internalNotice = $this->fakeInternalNoticeData();
        $this->response = $this->json('POST', '/api/internalNotices', $internalNotice);

        $this->assertApiResponse($internalNotice);
    }

    /**
     * @test
     */
    public function test_read_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $this->response = $this->json('GET', '/api/internalNotices/'.$internalNotice->id);

        $this->assertApiResponse($internalNotice->toArray());
    }

    /**
     * @test
     */
    public function test_update_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $editedInternalNotice = $this->fakeInternalNoticeData();

        $this->response = $this->json('PUT', '/api/internalNotices/'.$internalNotice->id, $editedInternalNotice);

        $this->assertApiResponse($editedInternalNotice);
    }

    /**
     * @test
     */
    public function test_delete_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $this->response = $this->json('DELETE', '/api/internalNotices/'.$internalNotice->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/internalNotices/'.$internalNotice->id);

        $this->response->assertStatus(404);
    }
}
