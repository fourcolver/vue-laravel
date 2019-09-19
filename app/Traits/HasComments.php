<?php

namespace App\Traits;

use BeyondCode\Comments\Traits\HasComments as OriginalHasTraits;
use BeyondCode\Comments\Contracts\Commentator;
use Illuminate\Database\Eloquent\Model;

trait HasComments
{
    use OriginalHasTraits;

    /**
     * Return all direct(parent_id null) comments for this model.
     *
     * @return MorphMany
     */
    public function comments()
    {
        return $this
            ->morphMany(config('comments.comment_class'), 'commentable')
            ->where("parent_id", null);
    }

    /**
     * Return all comments for this model.
     *
     * @return MorphMany
     */
    public function allComments()
    {
        return $this
            ->morphMany(config('comments.comment_class'), 'commentable');
    }

    /**
     * Attach a comment to this model.
     *
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function comment(string $comment, $parentId = null)
    {
        return $this->commentAsUser(auth()->user(), $comment, $parentId);
    }

    /**
     * Attach a comment to this model as a specific user.
     *
     * @param Model|null $user
     * @param string $comment
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function commentAsUser(?Model $user, string $comment, $parentId = null)
    {
        $commentClass = config('comments.comment_class');

        $comment = new $commentClass([
            'comment' => $comment,
            'is_approved' => ($user instanceof Commentator) ? ! $user->needsCommentApproval($this) : false,
            'user_id' => is_null($user) ? null : $user->getKey(),
            'commentable_id' => $this->getKey(),
            'commentable_type' => get_class(),
            'parent_id' => $parentId,
        ]);

        return $this->comments()->save($comment);
    }

}
