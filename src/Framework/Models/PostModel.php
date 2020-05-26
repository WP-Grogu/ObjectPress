<?php

namespace OP\Framework\Models;

use \Exception;
use OP\Framework\Helpers\PostHelper;
use OP\Framework\Utils\Media;
use OP\Framework\Models\Traits\PostAttributes;
use OP\Framework\Models\Traits\PostType;
use OP\Framework\Models\Traits\PostLanguage;
use OP\Framework\Models\Traits\PostAcf;
use OP\Framework\Models\Traits\PostQuery;

/**
 * @package  ObjectPress
 * @author   tgeorgel
 * @version  1.2
 * @access   public
 * @since    0.1
 */
abstract class PostModel
{
    use PostAttributes,
        PostType,
        PostLanguage,
        PostAcf,
        PostQuery;

    /**
     * Wordpress post's id
     *
     * @var int
     * @since 0.1
     */
    private $post_id;


    /**
     * Wordpress post object
     *
     * @var \WP_Post
     * @access private
     * @since 0.1
     */
    private $post;


    /**
     * Wordpress post type (CPT)
     *
     * @var string
     * @access private
     * @since 0.1
     */
    private static $post_type;


    /**
     * Post attributes
     *
     * @var array
     * @access private
     * @since 0.1
     */
    private $attributes = [];


    /**
     * Post attributes, filleable by child class
     *
     * @var array
     * @access private
     * @since 0.1
     */
    private $fillable = [];



    /******************************************/
    /*                                        */
    /*             Magic methods              */
    /*                                        */
    /******************************************/



    /**
     * Variable getter
     *
     * @param string $key Attribute to retreive
     * @return mixed
     * @access public
     * @since 0.1
     */
    public function __get(string $key)
    {
        return $this->attributes[$key] ?? false;
    }

    /**
     * Variable setter
     *
     * @param string $key Attribute to retreive
     * @return mixed
     * @access public
     * @since 0.1
     */
    public function __set(string $key, $value): void
    {
        if (key_exists($key, $this->fillable)) {
            $this->fillable[$key] = $value;
        }
    }


    /**
     * Model constructor
     * Retrieve post_type model from database and setup variables
     *
     * @param int|null $model_id (optionnal)
     * @param bool     $strict (optionnal) In strict mode, throw exception if post doesn't exist
     *
     * @return void
     * @since 0.1
     */
    public function __construct(?int $model_id = null, bool $strict = false)
    {
        $this->pTypeExistsOrFail();

        // Create new post if post_id is empty
        if ($model_id == null) {
            if ($strict === false) {
                $model_id = $this->create();
            } else {
                throw new Exception("Post id is required in strict mode");
            }
        }

        $post = get_post($model_id);

        if ($post == null) {
            throw new Exception("Failed to retreive post");
        } elseif ($post->post_type !== static::$post_type) {
            throw new Exception("Given post id is not part of the current model post_type");
        } else {
            $this->post_id = $model_id;
            $this->post = $post;
            $this->wpPostToAttributes($post);
        }
    }



    /******************************************/
    /*                                        */
    /*         Quick operand methods          */
    /*                                        */
    /******************************************/



    /**
     * Retrieve the post object from WP.
     * Set $refresh = true to get the post from database
     *
     * @param  bool   $refresh Refresh data from database
     * @return object
     * @since 0.1
     */
    public function get(bool $refresh = false)
    {
        if ($this->attributes == null || $refresh === true) {
            $this->wpPostToAttributes($this->getPost());
        }

        return $this->attributes;
    }


    /**
     * Create a new Model in database
     *
     * @param  array $attr Attributes to link to the post (optionnal)
     * @return int   $post_id
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/wp_insert_post/
     */
    protected function create(array $attr = [])
    {
        $post_id = wp_insert_post(
            $attr +
                [
                    'post_title'    => 'Draft title',
                    'post_name'     => sanitize_title($attr['post_title'] ?? 'Draft title'),
                    'post_status'   => 'draft',
                    'post_type'     => static::$post_type,
                ]
        );

        return $post_id;
    }


    /**
     * Get the post permalink
     *
     * @return string|false
     * @since 0.1
     */
    public function permalink()
    {
        return get_permalink($this->post_id);
    }


    /**
     * Publish the current post
     *
     * @return void
     * @since 0.1
     */
    public function publish()
    {
        wp_publish_post($this->post_id);
    }


    /**
     * Trash the current post
     *
     * @return void
     * @since 0.1
     */
    public function trash()
    {
        wp_trash_post($this->post_id);
    }


    /**
     * Delete permanently the current post
     *
     * @param bool $force_delete Optional. Whether to bypass trash and force deletion. Default false.
     * @return void
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/wp_delete_post/
     */
    public function delete(bool $force_delete = false)
    {
        wp_delete_post($this->post_id, $force_delete);
    }


    /**
     * Save post attributes into database
     *
     * @return void
     * @since 0.1
     */
    public function save()
    {
        $this->mergeFillableIntoAttributes();
        $this->setPostProperties(
            $this->attributesToPostArray()
        );
    }


    /**
     * Get the post creation date at the specified date_format (php ref)
     *
     * @param string $format Eg. 'd/m/y' for '18/02/19'
     *
     * @return string
     * @since 0.1
     */
    public function postDate($format = 'd/m/Y')
    {
        return get_the_date($format, $this->post_id);
    }



    /******************************************/
    /*                                        */
    /*               Thumbnails               */
    /*                                        */
    /******************************************/


    /**
     * Return post thumbnail
     *
     * @param string|array $size (optionnal)
     * @param string|array $attr (optionnal)
     *
     * @return string|false The post thumbnail image tag. False on failure (no thumbnail image)
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/get_the_post_thumbnail/
     */
    public function getPrintableThumbnail($size = 'post-thumbnail', $attr = '')
    {
        if (has_post_thumbnail($this->post_id)) {
            return get_the_post_thumbnail($this->post_id, $size, $attr);
        }
        return false;
    }


    /**
     * Return thumbnail information such as image dimensions and public url
     *
     * @param string|array $size (optionnal)
     * @return array|false
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
     */
    public function getThumbnailInfos($size = 'post-thumbnail')
    {
        return wp_get_attachment_image_src($this->getThumbnailID(), $size) ?? false;
    }


    /**
     * Return post thumbnail id
     *
     * @return int The post thumbnail image id
     * @since 0.1
     */
    public function getThumbnailID()
    {
        return (int) get_post_thumbnail_id($this->post_id);
    }


    /**
     * Return thumbnail url
     *
     * @param string|array $size (optionnal)
     * @return string (empty on failure)
     * @since 0.2
     *
     * @reference https://developer.wordpress.org/reference/functions/wp_get_attachment_image_src/
     */
    public function getThumbnailUrl($size = 'post-thumbnail')
    {
        return $this->getThumbnailInfos($size)[0] ?? '';
    }



    /**
     * Set post thumbnail from attachement ID
     *
     * @param int $thumbnail_id ID of the attachement (image)
     * @since 0.1
     *
     * @reference https://developer.wordpress.org/reference/functions/set_post_thumbnail/
     */
    public function setThumbail(int $thumbnail_id)
    {
        set_post_thumbnail($this->post_id, $thumbnail_id);
    }


    /**
     * Set thumbnail image from an url link
     *
     * @param string $url Url of the image
     * @param string $name Optionnal. The name of the image, default to urls's image name
     *
     * @return int $thumbnail_id
     * @throw Exception
     * @since 0.1
     */
    public function setThumbailFromUrl(string $url, string $name = ''): int
    {
        $media = new Media();

        $thumbnail_id = $media->insertImageFromUrl($url, $name, $this->post_id);
        $this->setThumbail($thumbnail_id);

        return $thumbnail_id;
    }



    /******************************************/
    /*                                        */
    /*          Properties & Metas            */
    /*                                        */
    /******************************************/



    /**
     * Update post's given property
     *
     * @param string $property property key to be affected
     * @param mixed $value value to set
     *
     * @return void
     * @since 0.1
     */
    public function setPostProperty(string $property, $value)
    {
        $post = $this->getPost(ARRAY_A);
        $post[$property] = $value;
        $this->updatePost($post);
    }


    /**
     * Update post's given properties
     *
     * @param array_a $args          Associative array [property_key => value]
     * @param bool    $set_permalink Set true to automatically generate permalink from post title (default: true)
     *
     * @return this
     * @chainable
     * @since 0.1
     */
    public function setPostProperties(array $args, bool $set_permalink = true)
    {
        $post = $this->getPost(ARRAY_A);

        // Generate permalink based on post title
        if ($set_permalink && isset($args['post_title']) && !isset($args['post_name'])) {
            $args['post_name'] = sanitize_title($args['post_title']);
        }

        foreach ($args as $key => $value) {
            $post[$key] = $value;
        }

        $this->updatePost($post);
        return $this;
    }


    /**
     * Generate a permalink from post post's title
     * Setup this as a fillable
     *
     * @return this
     * @chainable
     * @since 0.1
     */
    public function generatePermalink()
    {
        $this->fillable['name'] = sanitize_title($this->fillable['title']);
        return $this;
    }


    /**
     * Retrieve post metas from database
     *
     * @return array
     * @since 0.1
     */
    public function metas()
    {
        return get_post_custom($this->post_id);
    }


    /**
     * Retrieve post metas keys from database
     *
     * @return array
     * @since 0.1
     */
    public function metasKeys()
    {
        return array_keys($this->metas());
    }


    /**
     * Get a post meta based on meta key
     *
     * @param string $meta_key key colrresponding to the meta
     * @param bool   $single   optional $single see get_post_meta() single opt
     *
     * @return array|string|int
     * @since 0.1
     */
    public function getMeta(string $meta_key, bool $single = true)
    {
        return get_post_meta($this->post_id, $meta_key, $single);
    }


    /**
     * Update post meta
     *
     * @param string $key      name of post metas to be updated in the database
     * @param mixed  $value    value of post metas to be updated in the database
     * @param bool   $multiple tells is the meta key unique or not
     *
     * @return void
     * @since 0.1
     */
    public function setMeta(string $key, $value, $multiple = false)
    {
        if ($multiple === false) {
            return update_post_meta($this->post_id, $key, $value);
        } else {
            return add_post_meta($this->post_id, $key, $value, false);
        }
    }


    /**
     * Update post meta
     *
     * @param array $metas ['meta_key' => 'meta_value']
     *
     * @return void
     * @since 0.1
     */
    public function setMetas(array $metas)
    {
        foreach ($metas as $key => $value) {
            $this->setMeta($key, $value);
        }
    }



    /******************************************/
    /*                                        */
    /*              Taxonomies                */
    /*                                        */
    /******************************************/



    /**
     * Update Property's taxonomies.
     *
     * @param string $taxonomy taxonomy key
     * @param array  $terms    terms list
     *
     * @return void
     * @since 0.1
     */
    public function setTaxonomyTerms(string $taxonomy, array $terms)
    {
        wp_set_post_terms($this->post_id, [], $taxonomy, false);
        wp_set_post_terms($this->post_id, $terms, $taxonomy, false);
    }


    /**
     * Retrieve post taxonomies
     *
     * @param $hide_empty Weither should we get unused terms
     * @return array
     * @since 0.1
     */
    public function getTaxonomies($hide_empty = false)
    {
        $values     = [];
        $taxonomies = get_post_taxonomies($this->post_id);

        foreach ($taxonomies as $taxonomy) {
            $values[$taxonomy] = get_terms(
                [
                    'taxonomy' => $taxonomy,
                    'hide_empty' => $hide_empty
                ]
            );
        }
        return $values;
    }


    /**
     * Get post terms selected in a given taxonomy
     *
     * @param  string $taxonomy Taxonomy slug to search in
     * @return array
     * @since 0.1
     */
    public function getTaxonomyTerms(string $taxonomy)
    {
        return wp_get_post_terms($this->post_id, $taxonomy);
    }



    /******************************************/
    /*                                        */
    /*            Static methods              */
    /*                                        */
    /******************************************/



    /**
     * Get current post using wordpress magic function
     *
     * @return Model null on failure
     * @since 0.1
     */
    public static function current()
    {
        $id = get_the_id();

        if (!$id) {
            global $post;
            $id = $post->ID ?? false;
        }

        if (!$id) {
            return null;
        }

        return new static($id);
    }


    /**
     * Create a new Post. Returns self, false on failure
     *
     * @param array_a Post attributes (optionnal)
     *
     * @return this|false on failure
     * @since 0.1
     */
    public static function insert(array $attributes = [])
    {
        $post = new static();

        if ($post && $post->id) {
            $post->setPostProperties($attributes);
            return $post;
        }

        return false;
    }


    /**
     * Find the resource, given it's ID. Return false if post_id is not a member of model
     *
     * @param int $post_id Post_id
     *
     * @return This|false
     * @since 1.2.1
     */
    public static function find(string $post_id)
    {
        if (!static::belongsToModel($post_id)) {
            return false;
        }

        return new static($post_id);
    }


    /**
     * Find the ressource(s)
     *
     * @param array $post_ids Post ids to get
     *
     * @return array
     * @since 1.2.1
     */
    public static function collection(array $post_ids)
    {
        $collection = [];

        foreach ($post_ids as $post_id) {
            $item = static::find($post_id);

            if (is_bool($item) && $item === false) {
                continue;
            }

            $collection[] = $item;
        }

        return $collection;
    }


    /**
     * Check if the given post(s) are member of the current model
     * If you're giving an array of posts, reuturns true if ALL of them are members
     *
     * @param array|string|int|WP_Post $post Post(s) to checkup.
     *
     * @return bool
     */
    public static function belongsToModel($posts): bool
    {
        if (!$posts || (is_array($posts) && empty($posts))) {
            return false;
        }

        if (!is_array($posts)) {
            $posts = [$posts];
        }

        foreach ($posts as $post) {
            if (!PostHelper::isA($post, static::$post_type)) {
                return false;
            }
        }

        return true;
    }
}
