<?php namespace Tests\Repositories;

use App\Models\InternalNotice;
use App\Repositories\InternalNoticeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeInternalNoticeTrait;
use Tests\ApiTestTrait;

class InternalNoticeRepositoryTest extends TestCase
{
    use MakeInternalNoticeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var InternalNoticeRepository
     */
    protected $internalNoticeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->internalNoticeRepo = \App::make(InternalNoticeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_internal_notice()
    {
        $internalNotice = $this->fakeInternalNoticeData();
        $createdInternalNotice = $this->internalNoticeRepo->create($internalNotice);
        $createdInternalNotice = $createdInternalNotice->toArray();
        $this->assertArrayHasKey('id', $createdInternalNotice);
        $this->assertNotNull($createdInternalNotice['id'], 'Created InternalNotice must have id specified');
        $this->assertNotNull(InternalNotice::find($createdInternalNotice['id']), 'InternalNotice with given id must be in DB');
        $this->assertModelData($internalNotice, $createdInternalNotice);
    }

    /**
     * @test read
     */
    public function test_read_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $dbInternalNotice = $this->internalNoticeRepo->find($internalNotice->id);
        $dbInternalNotice = $dbInternalNotice->toArray();
        $this->assertModelData($internalNotice->toArray(), $dbInternalNotice);
    }

    /**
     * @test update
     */
    public function test_update_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $fakeInternalNotice = $this->fakeInternalNoticeData();
        $updatedInternalNotice = $this->internalNoticeRepo->update($fakeInternalNotice, $internalNotice->id);
        $this->assertModelData($fakeInternalNotice, $updatedInternalNotice->toArray());
        $dbInternalNotice = $this->internalNoticeRepo->find($internalNotice->id);
        $this->assertModelData($fakeInternalNotice, $dbInternalNotice->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_internal_notice()
    {
        $internalNotice = $this->makeInternalNotice();
        $resp = $this->internalNoticeRepo->delete($internalNotice->id);
        $this->assertTrue($resp);
        $this->assertNull(InternalNotice::find($internalNotice->id), 'InternalNotice should not exist in DB');
    }
}
