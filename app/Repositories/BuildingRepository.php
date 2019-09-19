<?php

namespace App\Repositories;

use App\Models\Building;
use InfyOm\Generator\Common\BaseRepository;
use Storage;

/**
 * Class BuildingRepository
 * @package App\Repositories
 * @version January 27, 2019, 7:57 pm UTC
 *
 * @method Building findWithoutFail($id, $columns = ['*'])
 * @method Building find($id, $columns = ['*'])
 * @method Building first($columns = ['*'])
 */
class BuildingRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'name' => 'like',
        'description' => 'like',
        'label' => 'like',
        'floor_nr' => 'like',
    ];

    protected $mimeToExtension = [
        "image/jpeg" =>  "jpg",
        "application/pdf" =>  "pdf",
        "image/png" =>  "png",
        "application/msword" =>  "doc",
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Building::class;
    }

    public function create(array $attributes)
    {
        if (isset($attributes['address'])) {
            unset($attributes['address']);
        }

        if (isset($attributes['district'])) {
            unset($attributes['district']);
        }

        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);
        $model = parent::create($attributes);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        if ($attributes['service_providers']) {
            $model->serviceProviders()->attach($attributes['service_providers']);
        }

        return $this->parserResult($model);
    }

    public function update(array $attributes, $id)
    {
        // Have to skip presenter to get a model not some data
        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = parent::update($attributes, $id);
        $this->skipPresenter($temporarySkipPresenter);

        $model = $this->updateRelations($model, $attributes);
        $model->save();

        if ($attributes['service_providers']) {
            $model->serviceProviders()->sync($attributes['service_providers']);
        }

        return $this->parserResult($model);
    }

    public function delete($id)
    {
        $this->applyScope();

        $temporarySkipPresenter = $this->skipPresenter;
        $this->skipPresenter(true);

        $model = $this->find($id);

        $this->skipPresenter($temporarySkipPresenter);
        $this->resetModel();

        $model->serviceProviders()->detach();

        $deleted = $model->delete();

        return $deleted;
    }

    public function uploadFiles(string $collectionName, string $dataBase64, Building $model)
    {
        if (!$data = base64_decode($dataBase64)) {
            return false;
        }

        $file  = finfo_open();
        $mimeType  = finfo_buffer($file, $data, FILEINFO_MIME_TYPE);
        finfo_close($file);

        if (!isset($this->mimeToExtension[$mimeType])){
            return false;
        }
        $extension = $this->mimeToExtension[$mimeType];

        $diskName = sprintf("buildings_%s", $collectionName);

        $media = $model->addMediaFromBase64($dataBase64)
            ->sanitizingFileName(function ($fileName) use ($extension) {
                return sprintf('%s.%s', str_slug($fileName), $extension);
            })
            ->toMediaCollection($collectionName, $diskName);

        return $media;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->model->count();
    }
}
