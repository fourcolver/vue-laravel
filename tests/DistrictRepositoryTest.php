<?php

use App\Models\District;
use App\Repositories\DistrictRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DistrictRepositoryTest extends TestCase
{
    use MakeDistrictTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DistrictRepository
     */
    protected $districtRepo;

    public function setUp()
    {
        parent::setUp();
        $this->districtRepo = App::make(DistrictRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDistrict()
    {
        $district = $this->fakeDistrictData();
        $createdDistrict = $this->districtRepo->create($district);
        $createdDistrict = $createdDistrict->toArray();
        $this->assertArrayHasKey('id', $createdDistrict);
        $this->assertNotNull($createdDistrict['id'], 'Created District must have id specified');
        $this->assertNotNull(District::find($createdDistrict['id']), 'District with given id must be in DB');
        $this->assertModelData($district, $createdDistrict);
    }

    /**
     * @test read
     */
    public function testReadDistrict()
    {
        $district = $this->makeDistrict();
        $dbDistrict = $this->districtRepo->find($district->id);
        $dbDistrict = $dbDistrict->toArray();
        $this->assertModelData($district->toArray(), $dbDistrict);
    }

    /**
     * @test update
     */
    public function testUpdateDistrict()
    {
        $district = $this->makeDistrict();
        $fakeDistrict = $this->fakeDistrictData();
        $updatedDistrict = $this->districtRepo->update($fakeDistrict, $district->id);
        $this->assertModelData($fakeDistrict, $updatedDistrict->toArray());
        $dbDistrict = $this->districtRepo->find($district->id);
        $this->assertModelData($fakeDistrict, $dbDistrict->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDistrict()
    {
        $district = $this->makeDistrict();
        $resp = $this->districtRepo->delete($district->id);
        $this->assertTrue($resp);
        $this->assertNull(District::find($district->id), 'District should not exist in DB');
    }
}
