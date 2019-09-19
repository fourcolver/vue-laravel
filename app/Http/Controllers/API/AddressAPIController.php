<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Address\CreateRequest;
use App\Http\Requests\API\Address\DeleteRequest;
use App\Http\Requests\API\Address\ListRequest;
use App\Http\Requests\API\Address\UpdateRequest;
use App\Http\Requests\API\Address\ViewRequest;
use App\Models\Address;
use App\Repositories\AddressRepository;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;

/**
 * Class AddressController
 * @package App\Http\Controllers\API
 */
class AddressAPIController extends AppBaseController
{
    /** @var  AddressRepository */
    private $addressRepository;

    /**
     * AddressAPIController constructor.
     * @param AddressRepository $addressRepo
     */
    public function __construct(AddressRepository $addressRepo)
    {
        $this->addressRepository = $addressRepo;
    }

    /**
     * @param ListRequest $request
     * @return Response
     * @throws \Exception
     *
     */
    public function index(ListRequest $request)
    {
        $this->addressRepository->pushCriteria(new RequestCriteria($request));
        $this->addressRepository->pushCriteria(new LimitOffsetCriteria($request));

        $getAll = $request->get('get_all', false);
        if ($getAll) {
            $addresses = $this->addressRepository->get();
            return $this->sendResponse($addresses->toArray(), 'Addresses retrieved successfully');
        }

        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $addresses = $this->addressRepository->with(['country', 'state'])->paginate($perPage);

        return $this->sendResponse($addresses->toArray(), 'Addresses retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     * @throws \Exception
     *
     */
    public function store(CreateRequest $request)
    {
        $input = $request->all();
        $input['country_id'] = 210;
        $addresses = $this->addressRepository->create($input);

        return $this->sendResponse($addresses->toArray(), 'Address saved successfully');
    }

    /**
     * @param int $id
     * @param ViewRequest $r
     * @return Response
     *
     */
    public function show($id, ViewRequest $r)
    {
        /** @var Address $address */
        $address = $this->addressRepository->with(['country', 'state'])->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        return $this->sendResponse($address->toArray(), 'Address retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws \Exception
     *
     */
    public function update($id, UpdateRequest $request)
    {
        $input = $request->all();
        $input['country_id'] = 210;

        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        $address = $this->addressRepository->update($input, $id);

        return $this->sendResponse($address->toArray(), 'Address updated successfully');
    }

    /**
     * @param int $id
     * @param DeleteRequest $r
     * @return Response
     *
     */
    public function destroy($id, DeleteRequest $r)
    {
        /** @var Address $address */
        $address = $this->addressRepository->findWithoutFail($id);

        if (empty($address)) {
            return $this->sendError('Address not found');
        }

        $address->delete();

        return $this->sendResponse($id, 'Address deleted successfully');
    }
}
