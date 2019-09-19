<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UnitApiTest extends TestCase
{
    use MakeUnitTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateUnit()
    {
        $unit = $this->fakeUnitData();
        $this->json('POST', '/api/v1/units', $unit);

        $this->assertApiResponse($unit);
    }

    /**
     * @test
     */
    public function testReadUnit()
    {
        $unit = $this->makeUnit();
        $this->json('GET', '/api/v1/units/'.$unit->id);

        $this->assertApiResponse($unit->toArray());
    }

    /**
     * @test
     */
    public function testUpdateUnit()
    {
        $unit = $this->makeUnit();
        $editedUnit = $this->fakeUnitData();

        $this->json('PUT', '/api/v1/units/'.$unit->id, $editedUnit);

        $this->assertApiResponse($editedUnit);
    }

    /**
     * @test
     */
    public function testDeleteUnit()
    {
        $unit = $this->makeUnit();
        $this->json('DELETE', '/api/v1/units/'.$unit->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/units/'.$unit->id);

        $this->assertResponseStatus(404);
    }
}
