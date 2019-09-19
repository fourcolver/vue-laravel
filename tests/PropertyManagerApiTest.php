<?php

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;

class PropertyManagerApiTest extends TestCase
{
    use MakePropertyManagerTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePropertyManager()
    {
        $propertyManager = $this->fakePropertyManagerData();
        $this->json('POST', '/api/v1/propertyManagers', $propertyManager);

        $this->assertApiResponse($propertyManager);
    }

    /**
     * @test
     */
    public function testReadPropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $this->json('GET', '/api/v1/propertyManagers/' . $propertyManager->id);

        $this->assertApiResponse($propertyManager->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $editedPropertyManager = $this->fakePropertyManagerData();

        $this->json('PUT', '/api/v1/propertyManagers/' . $propertyManager->id, $editedPropertyManager);

        $this->assertApiResponse($editedPropertyManager);
    }

    /**
     * @test
     */
    public function testDeletePropertyManager()
    {
        $propertyManager = $this->makePropertyManager();
        $this->json('DELETE', '/api/v1/propertyManagers/' . $propertyManager->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/propertyManagers/' . $propertyManager->id);

        $this->assertResponseStatus(404);
    }
}
