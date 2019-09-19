<?php

use App\Models\Unit;
use App\Repositories\UnitRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitRepositoryTest extends TestCase
{
    use MakeUnitTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var UnitRepository
     */
    protected $unitRepo;

    public function setUp()
    {
        parent::setUp();
        $this->unitRepo = App::make(UnitRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateUnit()
    {
        $unit = $this->fakeUnitData();
        $createdUnit = $this->unitRepo->create($unit);
        $createdUnit = $createdUnit->toArray();
        $this->assertArrayHasKey('id', $createdUnit);
        $this->assertNotNull($createdUnit['id'], 'Created Unit must have id specified');
        $this->assertNotNull(Unit::find($createdUnit['id']), 'Unit with given id must be in DB');
        $this->assertModelData($unit, $createdUnit);
    }

    /**
     * @test read
     */
    public function testReadUnit()
    {
        $unit = $this->makeUnit();
        $dbUnit = $this->unitRepo->find($unit->id);
        $dbUnit = $dbUnit->toArray();
        $this->assertModelData($unit->toArray(), $dbUnit);
    }

    /**
     * @test update
     */
    public function testUpdateUnit()
    {
        $unit = $this->makeUnit();
        $fakeUnit = $this->fakeUnitData();
        $updatedUnit = $this->unitRepo->update($fakeUnit, $unit->id);
        $this->assertModelData($fakeUnit, $updatedUnit->toArray());
        $dbUnit = $this->unitRepo->find($unit->id);
        $this->assertModelData($fakeUnit, $dbUnit->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteUnit()
    {
        $unit = $this->makeUnit();
        $resp = $this->unitRepo->delete($unit->id);
        $this->assertTrue($resp);
        $this->assertNull(Unit::find($unit->id), 'Unit should not exist in DB');
    }
}
