<?php

use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddressRepositoryTest extends TestCase
{
    use MakeAddressTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AddressRepository
     */
    protected $addressRepo;

    public function setUp()
    {
        parent::setUp();
        $this->addressRepo = App::make(AddressRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAddress()
    {
        $address = $this->fakeAddressData();
        $createdAddress = $this->addressRepo->create($address);
        $createdAddress = $createdAddress->toArray();
        $this->assertArrayHasKey('id', $createdAddress);
        $this->assertNotNull($createdAddress['id'], 'Created Address must have id specified');
        $this->assertNotNull(Address::find($createdAddress['id']), 'Address with given id must be in DB');
        $this->assertModelData($address, $createdAddress);
    }

    /**
     * @test read
     */
    public function testReadAddress()
    {
        $address = $this->makeAddress();
        $dbAddress = $this->addressRepo->find($address->id);
        $dbAddress = $dbAddress->toArray();
        $this->assertModelData($address->toArray(), $dbAddress);
    }

    /**
     * @test update
     */
    public function testUpdateAddress()
    {
        $address = $this->makeAddress();
        $fakeAddress = $this->fakeAddressData();
        $updatedAddress = $this->addressRepo->update($fakeAddress, $address->id);
        $this->assertModelData($fakeAddress, $updatedAddress->toArray());
        $dbAddress = $this->addressRepo->find($address->id);
        $this->assertModelData($fakeAddress, $dbAddress->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAddress()
    {
        $address = $this->makeAddress();
        $resp = $this->addressRepo->delete($address->id);
        $this->assertTrue($resp);
        $this->assertNull(Address::find($address->id), 'Address should not exist in DB');
    }
}
