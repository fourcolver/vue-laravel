<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressApiTest extends TestCase
{
    use MakeAddressTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAddress()
    {
        $address = $this->fakeAddressData();
        $this->json('POST', '/api/v1/addresses', $address);

        $this->assertApiResponse($address);
    }

    /**
     * @test
     */
    public function testReadAddress()
    {
        $address = $this->makeAddress();
        $this->json('GET', '/api/v1/addresses/'.$address->id);

        $this->assertApiResponse($address->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAddress()
    {
        $address = $this->makeAddress();
        $editedAddress = $this->fakeAddressData();

        $this->json('PUT', '/api/v1/addresses/'.$address->id, $editedAddress);

        $this->assertApiResponse($editedAddress);
    }

    /**
     * @test
     */
    public function testDeleteAddress()
    {
        $address = $this->makeAddress();
        $this->json('DELETE', '/api/v1/addresses/'.$address->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/addresses/'.$address->id);

        $this->assertResponseStatus(404);
    }
}
