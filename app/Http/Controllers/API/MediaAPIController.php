<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Media\BuildingDeleteRequest;
use App\Http\Requests\API\Media\BuildingUploadRequest;
use App\Http\Requests\API\Media\PostDeleteRequest;
use App\Http\Requests\API\Media\PostUploadRequest;
use App\Http\Requests\API\Media\ProductDeleteRequest;
use App\Http\Requests\API\Media\ProductUploadRequest;
use App\Http\Requests\API\Media\SRequestDeleteRequest;
use App\Http\Requests\API\Media\SRequestUploadRequest;
use App\Http\Requests\API\Media\TenantDeleteRequest;
use App\Http\Requests\API\Media\TenantUploadRequest;
use App\Models\Building;
use App\Repositories\AddressRepository;
use App\Repositories\BuildingRepository;
use App\Repositories\PostRepository;
use App\Repositories\ProductRepository;
use App\Repositories\ServiceRequestRepository;
use App\Repositories\TenantRepository;
use App\Transformers\MediaTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Validator;

/**
 * Class MediaController
 * @package App\Http\Controllers\API
 */
class MediaAPIController extends AppBaseController
{
    /** @var  BuildingRepository */
    private $buildingRepository;

    /** @var  AddressRepository */
    private $addressRepository;

    /** @var  PostRepository */
    private $postRepository;

    /** @var  ProductRepository */
    private $productRepository;

    /** @var  TenantRepository */
    private $tenantRepository;

    /** @var  ServiceRequestRepository */
    private $serviceRequestRepository;

    /**
     * MediaAPIController constructor.
     * @param BuildingRepository $buildingRepo
     * @param AddressRepository $addrRepo
     * @param PostRepository $postRepo
     * @param ProductRepository $productRepo
     * @param TenantRepository $tenantRepo
     * @param ServiceRequestRepository $serviceRequestRepo
     */
    public function __construct(
        BuildingRepository $buildingRepo,
        AddressRepository $addrRepo,
        PostRepository $postRepo,
        ProductRepository $productRepo,
        TenantRepository $tenantRepo,
        ServiceRequestRepository $serviceRequestRepo
    )
    {
        $this->buildingRepository = $buildingRepo;
        $this->addressRepository = $addrRepo;
        $this->postRepository = $postRepo;
        $this->productRepository = $productRepo;
        $this->tenantRepository = $tenantRepo;
        $this->serviceRequestRepository = $serviceRequestRepo;
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/buildings/{building_id}/media",
     *      summary="Store a newly created Building Media in storage",
     *      tags={"Building"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Building")
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
     *                  ref="#/definitions/Building"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function buildingUpload(int $id, BuildingUploadRequest $request)
    {
        $rules = array(
            'house_rules_upload' => 'required_without:operating_instructions_upload|string',
            'operating_instructions_upload' => 'required_without:house_rules_upload|string',
        );

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }

        /** @var Building $building */
        $building = $this->buildingRepository->findWithoutFail($id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $collectionName = '';
        $data = '';
        if ($request->has('house_rules_upload')) {
            $collectionName = 'house_rules';
            $data = $request->get('house_rules_upload', '');
        }

        if ($request->has('operating_instructions_upload')) {
            $collectionName = 'operating_instructions';
            $data = $request->get('operating_instructions_upload', '');
        }

        if (!$media = $this->buildingRepository->uploadFiles($collectionName, $data, $building)) {
            return $this->sendError('Media upload error');
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, 'Media saved successfully');
    }

    /**
     * @param int $building_id
     * @param int $media_id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/building/{building_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Building"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function buildingDestroy(int $building_id, int $media_id, BuildingDeleteRequest $r)
    {
        /** @var Building $building */
        $building = $this->buildingRepository->with('media')->findWithoutFail($building_id);
        if (empty($building)) {
            return $this->sendError('Building not found');
        }

        $media = $building->media->find($media_id);
        if (empty($media)) {
            return $this->sendError('Media not found');
        }

        $media->delete();

        return $this->sendResponse($media_id, 'Media deleted successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/posts/{post_id}/media",
     *      summary="Store a newly created Post Media in storage",
     *      tags={"Listing"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Post")
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
     *                  ref="#/definitions/Post"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function postUpload(int $id, PostUploadRequest $request)
    {
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Building not found');
        }

        $data = $request->get('media', '');
        if (!$media = $this->postRepository->uploadFile('media', $data, $post)) {
            return $this->sendError('Media upload error');
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, 'Media saved successfully');
    }

    /**
     * @param int $id
     * @param int $media_id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/post/{post_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Listing"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function postDestroy(int $id, int $media_id, PostDeleteRequest $r)
    {
        $post = $this->postRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Post not found');
        }

        $media = $post->media->find($media_id);
        if (empty($media)) {
            return $this->sendError('Media not found');
        }

        $media->delete();

        return $this->sendResponse($media_id, 'Media deleted successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenants/{tenant_id}/media",
     *      summary="Store a newly created Tenant Media in storage",
     *      tags={"Tenant"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Post")
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
     *                  ref="#/definitions/Tenant"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function tenantUpload(int $id, TenantUploadRequest $request)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);
        if (empty($tenant)) {
            return $this->sendError('Tenant not found');
        }

        $data = $request->get('media', '');
        if (!$media = $this->tenantRepository->uploadFile('media', $data, $tenant)) {
            return $this->sendError('Media upload error');
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, 'Media saved successfully');
    }

    /**
     * @param int $id
     * @param int $media_id
     * @param TenantDeleteRequest $r
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenants/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Tenant"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function tenantDestroy(int $id, int $media_id, TenantDeleteRequest $r)
    {
        $tenant = $this->tenantRepository->findWithoutFail($id);
        if (empty($tenant)) {
            return $this->sendError('Post not found');
        }

        $media = $tenant->media->find($media_id);
        if (empty($media)) {
            return $this->sendError('Media not found');
        }

        $media->delete();

        return $this->sendResponse($media_id, 'Media deleted successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/requests/{id}/media",
     *      summary="Store a newly created Request Media in storage",
     *      tags={"ServiceRequest"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ServiceRequest")
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
     *                  ref="#/definitions/ServiceRequest"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function serviceRequestUpload(int $id, SRequestUploadRequest $request)
    {
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Service Request not found');
        }

        $data = $request->get('media', '');
        if (!$media = $this->serviceRequestRepository->uploadFile('media', $data, $serviceRequest)) {
            return $this->sendError('Media upload error');
        }

        $a = $this->newRequestAudit($serviceRequest->id);
        $a->event = 'media_uploaded';
        $a->new_values = [
            'media_id' => $media->id,
            'media_url' => $media->getFullUrl(),
        ];
        $a->save();

        $this->serviceRequestRepository->notifyMedia($serviceRequest, \Auth::user(), $media);

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, 'Media saved successfully');
    }

    /**
     * @param int $id
     * @param int $media_id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/requests/{id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"ServiceRequest"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function serviceRequestDestroy(int $id, int $media_id, SRequestDeleteRequest $r)
    {
        $serviceRequest = $this->serviceRequestRepository->findWithoutFail($id);
        if (empty($serviceRequest)) {
            return $this->sendError('Post not found');
        }

        $media = $serviceRequest->media->find($media_id);
        if (empty($media)) {
            return $this->sendError('Media not found');
        }

        $media->delete();

        $a = $this->newRequestAudit($serviceRequest->id);
        $a->event = 'media_deleted';
        $a->old_values = [
            'media_id' => $media->id,
            'media_url' => $media->getFullUrl(),
        ];
        $a->save();

        return $this->sendResponse($media_id, 'Media deleted successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/products/{product_id}/media",
     *      summary="Store a newly created product Media in storage",
     *      tags={"Marketplace"},
     *      description="Store Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Media that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Product")
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
     *                  ref="#/definitions/Product"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function productUpload(int $id, ProductUploadRequest $request)
    {
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            return $this->sendError('Building not found');
        }

        $data = $request->get('media', '');
        if (!$media = $this->productRepository->uploadFile('media', $data, $product)) {
            return $this->sendError('Media upload error');
        }

        $response = (new MediaTransformer)->transform($media);
        return $this->sendResponse($response, 'Media saved successfully');
    }

    /**
     * @param int $id
     * @param int $media_id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/products/{product_id}/media/{media_id}",
     *      summary="Remove the specified Media from storage",
     *      tags={"Marketplace"},
     *      description="Delete Media",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Media",
     *          type="integer",
     *          required=true,
     *          in="path"
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
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function productDestroy(int $id, int $media_id, ProductDeleteRequest $r)
    {
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $media = $product->media->find($media_id);
        if (empty($media)) {
            return $this->sendError('Media not found');
        }

        $media->delete();

        return $this->sendResponse($media_id, 'Media deleted successfully');
    }
}
