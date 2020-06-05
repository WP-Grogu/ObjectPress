<?php

namespace OP\Framework\Models\Traits;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  0.1
 * @access   public
 * @since    0.1
 */
trait PostAttributes
{
    /**
     * Transform WP_Post OP Post to attributes
     *
     * @param \WP_Post $post
     * @return void
     * @since 0.1
     */
    protected function wpPostToAttributes(\WP_Post $post)
    {
        $this->attributes['id']                     = $post->ID;
        $this->attributes['author']                 = $post->post_author;
        $this->attributes['date']                   = $post->post_date;
        $this->attributes['date_gmt']               = $post->post_date_gmt;
        $this->attributes['title']                  = $post->post_title;
        $this->attributes['content']                = $post->post_content;
        $this->attributes['excerpt']                = $post->post_excerpt;
        $this->attributes['status']                 = $post->post_status;
        $this->attributes['comment_status']         = $post->comment_status;
        $this->attributes['ping_status']            = $post->ping_status;
        $this->attributes['password']               = $post->post_password;
        $this->attributes['name']                   = $post->post_name;
        $this->attributes['modified']               = $post->post_modified;
        $this->attributes['modified_gmt']           = $post->post_modified_gmt;
        $this->attributes['content_filtered']       = $post->post_content_filtered;
        $this->attributes['parent']                 = $post->post_parent;
        $this->attributes['guid']                   = $post->guid;
        $this->attributes['type']                   = $post->post_type;
        $this->attributes['mime_type']              = $post->post_mime_type;
        $this->attributes['comment_count']          = $post->comment_count;
        $this->attributes['filter']                 = $post->filter;

        $fillable = $this->attributes;

        unset($fillable['id'], $fillable['type']);

        $this->fillable = $fillable;
    }


    /**
     * Transform post attributes to WP_post object
     *
     * @return array
     * @since 0.1
     */
    protected function attributesToPostArray(): array
    {
        $post = $this->getPost(ARRAY_A);

        $post['post_author']              = $this->attributes['author'];
        $post['post_date']                = $this->attributes['date'];
        $post['post_date_gmt']            = $this->attributes['date_gmt'];
        $post['post_title']               = $this->attributes['title'];
        $post['post_content']             = $this->attributes['content'];
        $post['post_excerpt']             = $this->attributes['excerpt'];
        $post['post_status']              = $this->attributes['status'];
        $post['comment_status']           = $this->attributes['comment_status'];
        $post['ping_status']              = $this->attributes['status'];
        $post['post_password']            = $this->attributes['password'];
        $post['post_name']                = $this->attributes['name'];
        $post['post_modified']            = $this->attributes['modified'];
        $post['post_modified_gmt']        = $this->attributes['modified_gmt'];
        $post['post_content_filtered']    = $this->attributes['content_filtered'];
        $post['post_parent']              = $this->attributes['parent'];
        $post['guid']                     = $this->attributes['guid'];
        $post['post_mime_type']           = $this->attributes['mime_type'];
        $post['comment_count']            = $this->attributes['comment_count'];
        $post['filter']                   = $this->attributes['filter'];

        return $post;
    }


    /**
     * Merge new fillable fields into attributes
     *
     * @return void
     * @since 0.1
     */
    public function mergeFillableIntoAttributes(): void
    {
        $this->attributes = $this->fillable + $this->attributes;
    }


    /**
     * Retrieve post from database
     *
     * @param string $output One of OBJECT, ARRAY_A, or ARRAY_N
     * @return \WP_post|array
     *
     * @reference https://developer.wordpress.org/reference/functions/get_post/
     * @since 0.1
     */
    public function getPost($output = OBJECT)
    {
        $this->post = get_post($this->post_id, $output);
        return $this->post;
    }


    /**
     * Update post to database
     *
     * @param WP_Post|array $post post data to be updated
     *
     * @return this
     * @since 0.1
     */
    protected function updatePost($post)
    {
        wp_update_post($post);
        $this->get(true);
        return $this;
    }
}
