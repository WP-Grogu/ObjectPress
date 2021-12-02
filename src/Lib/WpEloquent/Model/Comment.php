<?php

namespace OP\Lib\WpEloquent\Model;

use OP\Lib\WpEloquent\Model;
use OP\Lib\WpEloquent\Concerns\Aliases;
use OP\Lib\WpEloquent\Concerns\MetaFields;
use OP\Lib\WpEloquent\Concerns\CustomTimestamps;
use OP\Lib\WpEloquent\Model\Builder\CommentBuilder;

/**
 * Class Comment
 *
 * @package OP\Lib\WpEloquent\Model
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Comment extends Model
{
    use MetaFields,
        CustomTimestamps,
        Aliases;

    const CREATED_AT = 'comment_date';
    const UPDATED_AT = null;

    /**
     * @var string
     */
    protected $table = 'comments';

    /**
     * @var string
     */
    protected $primaryKey = 'comment_ID';

    /**
     * @var array
     */
    protected $dates = ['comment_date'];

    /**
     * @var array
     */
    protected static $aliases = [
        'id'           => 'comment_ID',
        'post_id'      => 'comment_post_ID',
        'author'       => 'comment_author',
        'author_email' => 'comment_author_email',
        'author_url'   => 'comment_author_url',
        'author_ip'    => 'comment_author_IP',
        'created_at'   => 'comment_date',
        'date_gmt'     => 'comment_date_gmt',
        'content'      => 'comment_content',
        'karma'        => 'comment_karma',
        'approved'     => 'comment_approved',
        'agent'        => 'comment_agent',
        'type'         => 'comment_type',
        'parent'       => 'comment_parent',
    ];

    /**
     * Find a comment by post ID.
     *
     * @param int $postId
     * @return Comment
     */
    public static function findByPostId($postId)
    {
        return (new static())
            ->where('comment_post_ID', $postId)
            ->get();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post()
    {
        return $this->belongsTo(Post::class, 'comment_post_ID');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent()
    {
        return $this->original();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function original()
    {
        return $this->belongsTo(Comment::class, 'comment_parent');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function replies()
    {
        return $this->hasMany(Comment::class, 'comment_parent');
    }

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->attributes['comment_approved'] == 1;
    }

    /**
     * @return bool
     */
    public function isReply()
    {
        return $this->attributes['comment_parent'] > 0;
    }

    /**
     * @return bool
     */
    public function hasReplies()
    {
        return $this->replies->count() > 0;
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     * @return CommentBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new CommentBuilder($query);
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAt($value)
    {
        //
    }
}
