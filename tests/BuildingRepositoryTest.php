<?php

use App\Models\Building;
use App\Repositories\BuildingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BuildingRepositoryTest extends TestCase
{
    use MakeBuildingTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BuildingRepository
     */
    protected $buildingRepo;

    public function setUp()
    {
        parent::setUp();
        $this->buildingRepo = App::make(BuildingRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBuilding()
    {
        $building = $this->fakeBuildingData();
        $createdBuilding = $this->buildingRepo->create($building);
        $createdBuilding = $createdBuilding->toArray();
        $this->assertArrayHasKey('id', $createdBuilding);
        $this->assertNotNull($createdBuilding['id'], 'Created Building must have id specified');
        $this->assertNotNull(Building::find($createdBuilding['id']), 'Building with given id must be in DB');
        $this->assertModelData($building, $createdBuilding);
    }

    /**
     * @test read
     */
    public function testReadBuilding()
    {
        $building = $this->makeBuilding();
        $dbBuilding = $this->buildingRepo->find($building->id);
        $dbBuilding = $dbBuilding->toArray();
        $this->assertModelData($building->toArray(), $dbBuilding);
    }

    /**
     * @test update
     */
    public function testUpdateBuilding()
    {
        $building = $this->makeBuilding();
        $fakeBuilding = $this->fakeBuildingData();
        $updatedBuilding = $this->buildingRepo->update($fakeBuilding, $building->id);
        $this->assertModelData($fakeBuilding, $updatedBuilding->toArray());
        $dbBuilding = $this->buildingRepo->find($building->id);
        $this->assertModelData($fakeBuilding, $dbBuilding->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBuilding()
    {
        $building = $this->makeBuilding();
        $resp = $this->buildingRepo->delete($building->id);
        $this->assertTrue($resp);
        $this->assertNull(Building::find($building->id), 'Building should not exist in DB');
    }
}
