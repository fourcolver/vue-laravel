<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\RealEstate\UpdateRequest;
use App\Http\Requests\API\RealEstate\ViewRequest;
use App\Models\RealEstate;
use App\Models\User;
use App\Repositories\AddressRepository;
use App\Repositories\RealEstateRepository;
use App\Transformers\RealEstateTransformer;
use Cache;
use Illuminate\Http\Response;

/**
 * Class RealEstateController
 * @package App\Http\Controllers\API
 */
class RealEstateAPIController extends AppBaseController
{
    /** @var  RealEstateRepository */
    private $realEstateRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    public function __construct(RealEstateRepository $realEstateRepo, AddressRepository $addressRepo)
    {
        $this->realEstateRepository = $realEstateRepo;
        $this->addressRepository = $addressRepo;
    }

    /**
     * @return Response
     *
     * @SWG\Get(
     *      path="/realEstate/",
     *      summary="Display the RealEstate",
     *      tags={"RealEstate"},
     *      description="Get RealEstate",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/RealEstate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show(ViewRequest $r)
    {
        /** @var RealEstate $realEstate */
        $realEstate = $this->realEstateRepository->first();
        if (empty($realEstate)) {
            return $this->sendError('Real Estate not found');
        }

        $news_receiver_ids = $realEstate->news_receiver_ids ?? [];
        $realEstate->news_receivers = User::whereIn('id', $news_receiver_ids)->get();

        $response = (new RealEstateTransformer)->transform($realEstate);
        return $this->sendResponse($response, 'Real Estate retrieved successfully');
    }

    /**
     * @param UpdateRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/realEstate",
     *      summary="Update the RealEstate in storage",
     *      tags={"RealEstate"},
     *      description="Update RealEstate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="RealEstate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/RealEstate")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/RealEstate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update(UpdateRequest $request)
    {
        $input = $request->all();

        /** @var RealEstate $realEstate */
        $realEstate = $this->realEstateRepository->first();
        if (empty($realEstate)) {
            return $this->sendError('Real Estate not found');
        }

        // image upload
        $fileData = base64_decode($request->get('logo_upload', ''));
        if ($fileData) {
            try {
                $input['logo'] = $this->realEstateRepository->uploadImage($fileData, $realEstate);
            } catch (\Exception $e) {
                return $this->sendError('User image upload: ' . $e->getMessage());
            }
        }

        try {
            if (isset($input['address'])) {
                $this->addressRepository->update($input['address'], $realEstate->address_id);
            }
            $input['address_id'] = $realEstate->address_id;
            $realEstate = $this->realEstateRepository->update($input, $realEstate->id);
            // Forget weather so the next request to weather
            // brings info from the new location
            Cache::forget('weather_json');
        } catch (\Exception $e) {
            return $this->sendError('RealEstate updated error');
        }
        $realEstate->news_receivers = User::whereIn('id', $realEstate->news_receiver_ids)->get();

        $response = (new RealEstateTransformer)->transform($realEstate);
        return $this->sendResponse($response, 'RealEstate updated successfully');
    }
}
