<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreateTranslationAPIRequest;
use App\Http\Requests\API\UpdateTranslationAPIRequest;
use App\Models\Translation;
use App\Repositories\TranslationRepository;
use Illuminate\Http\Request;
use Response;

/**
 * Class TranslationController
 * @package App\Http\Controllers\API
 */
class TranslationAPIController extends AppBaseController
{
    /** @var  TranslationRepository */
    private $translationRepository;

    public function __construct(TranslationRepository $translationRepo)
    {
        $this->translationRepository = $translationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/translations",
     *      summary="Get a listing of the Translations.",
     *      tags={"Translation"},
     *      description="Get all Translations",
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
     *                  @SWG\Items(ref="#/definitions/Translation")
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
        $translations = $this->translationRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($translations->toArray(), 'Translations retrieved successfully');
    }

    /**
     * @param CreateTranslationAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/translations",
     *      summary="Store a newly created Translation in storage",
     *      tags={"Translation"},
     *      description="Store Translation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Translation that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Translation")
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
     *                  ref="#/definitions/Translation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTranslationAPIRequest $request)
    {
        $input = $request->all();

        $translations = $this->translationRepository->create($input);

        return $this->sendResponse($translations->toArray(), 'Translation saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/translations/{id}",
     *      summary="Display the specified Translation",
     *      tags={"Translation"},
     *      description="Get Translation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Translation",
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
     *                  ref="#/definitions/Translation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var Translation $translation */
        $translation = $this->translationRepository->find($id);

        if (empty($translation)) {
            return $this->sendError('Translation not found');
        }

        return $this->sendResponse($translation->toArray(), 'Translation retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTranslationAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/translations/{id}",
     *      summary="Update the specified Translation in storage",
     *      tags={"Translation"},
     *      description="Update Translation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Translation",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="Translation that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/Translation")
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
     *                  ref="#/definitions/Translation"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTranslationAPIRequest $request)
    {
        $input = $request->all();

        /** @var Translation $translation */
        $translation = $this->translationRepository->find($id);

        if (empty($translation)) {
            return $this->sendError('Translation not found');
        }

        $translation = $this->translationRepository->update($input, $id);

        return $this->sendResponse($translation->toArray(), 'Translation updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/translations/{id}",
     *      summary="Remove the specified Translation from storage",
     *      tags={"Translation"},
     *      description="Delete Translation",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of Translation",
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
    public function destroy($id)
    {
        /** @var Translation $translation */
        $translation = $this->translationRepository->find($id);

        if (empty($translation)) {
            return $this->sendError('Translation not found');
        }

        $translation->delete();

        return $this->sendResponse($id, 'Translation deleted successfully');
    }
}
