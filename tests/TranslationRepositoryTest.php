<?php

use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TranslationRepositoryTest extends TestCase
{
    use MakeTranslationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var TranslationRepository
     */
    protected $translationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->translationRepo = App::make(TranslationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateTranslation()
    {
        $translation = $this->fakeTranslationData();
        $createdTranslation = $this->translationRepo->create($translation);
        $createdTranslation = $createdTranslation->toArray();
        $this->assertArrayHasKey('id', $createdTranslation);
        $this->assertNotNull($createdTranslation['id'], 'Created Translation must have id specified');
        $this->assertNotNull(Translation::find($createdTranslation['id']), 'Translation with given id must be in DB');
        $this->assertModelData($translation, $createdTranslation);
    }

    /**
     * @test read
     */
    public function testReadTranslation()
    {
        $translation = $this->makeTranslation();
        $dbTranslation = $this->translationRepo->find($translation->id);
        $dbTranslation = $dbTranslation->toArray();
        $this->assertModelData($translation->toArray(), $dbTranslation);
    }

    /**
     * @test update
     */
    public function testUpdateTranslation()
    {
        $translation = $this->makeTranslation();
        $fakeTranslation = $this->fakeTranslationData();
        $updatedTranslation = $this->translationRepo->update($fakeTranslation, $translation->id);
        $this->assertModelData($fakeTranslation, $updatedTranslation->toArray());
        $dbTranslation = $this->translationRepo->find($translation->id);
        $this->assertModelData($fakeTranslation, $dbTranslation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteTranslation()
    {
        $translation = $this->makeTranslation();
        $resp = $this->translationRepo->delete($translation->id);
        $this->assertTrue($resp);
        $this->assertNull(Translation::find($translation->id), 'Translation should not exist in DB');
    }
}
