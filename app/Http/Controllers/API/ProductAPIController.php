<?php

namespace App\Http\Controllers\API;

use App\Criteria\Common\RequestCriteria;
use App\Criteria\Products\FilterByTypeCriteria;
use App\Criteria\Products\FilterByUserCriteria;
use App\Criteria\Products\FilterByStatusCriteria;
use App\Criteria\Products\FilterByDistrictCriteria;
use App\Notifications\ProductLiked;
use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\Product\CreateRequest;
use App\Http\Requests\API\Product\DeleteRequest;
use App\Http\Requests\API\Product\PublishRequest;
use App\Http\Requests\API\Product\UpdateRequest;
use App\Http\Requests\API\Product\ViewRequest;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Repositories\RealEstateRepository;
use App\Transformers\ProductTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;

/**
 * Class ProductController
 * @package App\Http\Controllers\API
 */
class ProductAPIController extends AppBaseController
{
    /** @var  ProductRepository */
    private $productRepository;
    /**
     * @var RealEstateRepository
     */
    private $realEstateRepository;
    /**
     * @var ProductTransformer
     */
    private $transformer;
    /**
     * @var UserTransformer
     */
    private $uTransformer;

    /**
     * ProductAPIController constructor.
     * @param ProductRepository $productRepo
     * @param RealEstateRepository $reRepo
     * @param ProductTransformer $t
     * @param UserTransformer $ut
     */
    public function __construct(
        ProductRepository $productRepo,
        RealEstateRepository $reRepo,
        ProductTransformer $t,
        UserTransformer $ut
    )
    {
        $this->productRepository = $productRepo;
        $this->realEstateRepository = $reRepo;
        $this->transformer = $t;
        $this->uTransformer = $ut;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Get(
     *      path="/products",
     *      summary="Get a listing of the Products.",
     *      tags={"Marketplace"},
     *      description="Get all Products",
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
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/Product")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->productRepository->pushCriteria(new RequestCriteria($request));
        $this->productRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->productRepository->pushCriteria(new FilterByUserCriteria($request));
        $this->productRepository->pushCriteria(new FilterByTypeCriteria($request));
        $this->productRepository->pushCriteria(new FilterByStatusCriteria($request));
        $this->productRepository->pushCriteria(new FilterByDistrictCriteria($request));
        
        $perPage = $request->get('per_page', env('APP_PAGINATE', 10));
        $products = $this->productRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
        ])->orderBy('published_at', 'desc')->orderBy('created_at', 'desc')
            ->paginate($perPage);
        $products->getCollection()->loadCount('allComments');

        $out = $this->transformer->transformPaginator($products);
        return $this->sendResponse($out, 'Products retrieved successfully');
    }

    /**
     * @param CreateRequest $request
     * @return Response
     * @throws \Exception
     * @SWG\Post(
     *      path="/products",
     *      summary="Store a newly created Product in storage",
     *      tags={"Marketplace"},
     *      description="Store Product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Product that should be stored",
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
    public function store(CreateRequest $request)
    {
        $input = $request->all();
        $input['user_id'] = \Auth::id();
        $input['status'] = Product::StatusUnpublished;

        $realEstate = $this->realEstateRepository->first();
        $input['needs_approval'] = false;
        if ($realEstate) {
            $input['needs_approval'] = $realEstate->marketplace_approval_enable;
        }

        $product = $this->productRepository->create($input);

        $data = $this->transformer->transform($product);
        return $this->sendResponse($data, 'Product saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/products/{id}",
     *      summary="Display the specified Product",
     *      tags={"Marketplace"},
     *      description="Get Product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
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
    public function show($id, ViewRequest $request)
    {
        /** @var Product $product */
        $product = $this->productRepository->with([
            'media',
            'user.tenant',
            'likesCounter',
            'likes' => function($q){$q->take(3);},
            'likes.user',
        ])->withCount('allComments')->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }
        $product->likers = $product->collectLikers();

        $data = $this->transformer->transform($product);
        return $this->sendResponse($data, 'Product retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRequest $request
     * @return Response
     * @throws \Exception
     *
     * @SWG\Put(
     *      path="/products/{id}",
     *      summary="Update the specified Product in storage",
     *      tags={"Marketplace"},
     *      description="Update Product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Product that should be updated",
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
    public function update($id, UpdateRequest $request)
    {
        $input = $request->only(Product::Fillable);

        /** @var Product $product */
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $product = $this->productRepository->update($input, $id);

        $data = $this->transformer->transform($product);
        return $this->sendResponse($data, 'Product updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/products/{id}",
     *      summary="Remove the specified Product from storage",
     *      tags={"Marketplace"},
     *      description="Delete Product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
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
    public function destroy($id, DeleteRequest $request)
    {
        /** @var Product $product */
        $product = $this->productRepository->findWithoutFail($id);

        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $product->delete();

        return $this->sendResponse($id, 'Product deleted successfully');
    }

    /**
     * @return Response
     *
     * @SWG\Post(
     *      path="/products/{id}/like",
     *      summary="Like a product",
     *      tags={"Marketplace"},
     *      description="Like a Product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
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
     *                  type="object",
     *                  description="logged in user"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function like($id)
    {
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $u = \Auth::user();
        $u->like($product);

        // if logged in user is tenant and
        // author of product is tenant and
        // author of product is different than liker
        if ($u->tenant && $product->user->tenant && $u->id != $product->user_id) {
            $product->user->notify(new ProductLiked($product, $u->tenant));
        }
        return $this->sendResponse($this->uTransformer->transform($u),
            'Product liked successfully');
    }

    /**
     * @return Response
     *
     * @SWG\Post(
     *      path="/products/{id}/unlike",
     *      summary="Unlike a Product",
     *      tags={"Marketplace"},
     *      description="Unlike a product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
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
     *                  type="object",
     *                  description="logged in user"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function unlike($id)
    {
        $product = $this->productRepository->findWithoutFail($id);
        if (empty($product)) {
            return $this->sendError('Product not found');
        }

        $u = \Auth::user();
        $u->unlike($product);
        return $this->sendResponse($this->uTransformer->transform($u),
            'Product unliked successfully');
    }

    /**
     * @param PublishRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/products/{id}/publish",
     *      summary="Publish a product",
     *      tags={"Marketplace"},
     *      description="Publish a product",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Product",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="status",
     *          in="body",
     *          type="integer",
     *          format="int32",
     *          description="The new status of the product",
     *          required=true,
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
    public function publish($id, PublishRequest $request)
    {
        $newStatus = $request->get('status');
        $post = $this->productRepository->findWithoutFail($id);
        if (empty($post)) {
            return $this->sendError('Product not found');
        }

        $post = $this->productRepository->setStatus($id, $newStatus);

        return $this->sendResponse($id, 'Product published successfully');
    }
}
