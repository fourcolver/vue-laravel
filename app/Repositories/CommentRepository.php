<?php

namespace App\Repositories;

use App\Models\Comment;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class CommentRepository
 * @package App\Repositories
 * @version February 11, 2019, 6:22 pm UTC
 *
 * @method Comment findWithoutFail($id, $columns = ['*'])
 * @method Comment find($id, $columns = ['*'])
 * @method Comment first($columns = ['*'])
*/
class CommentRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Comment::class;
    }

    public function update(array $attributes, $id)
    {
        $model = Comment::find($id);
        $model->comment = $attributes['comment'];
        $model->save();
        return $model;
    }

    public function destroy(Comment $comment)
    {
        if ($comment->children()->count()) {
            $this->update(['comment' => ''], $comment->id);

            return $comment->fresh();
        }

        $comment->delete();
        return $comment;
    }
}
