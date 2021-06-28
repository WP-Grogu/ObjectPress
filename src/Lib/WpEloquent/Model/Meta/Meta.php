<?php

namespace OP\Lib\WpEloquent\Model\Meta;

use Exception;
use ReflectionClass;
use OP\Lib\WpEloquent\Model;
use OP\Lib\WpEloquent\Model\Collection\MetaCollection;

/**
 * Class Meta
 *
 * @package OP\Lib\WpEloquent\Model\Meta
 * @author Junior Grossi <juniorgro@gmail.com>
 */
abstract class Meta extends Model
{
    /**
     * @var string
     */
    protected $primaryKey = 'meta_id';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    protected $appends = ['value'];

    /**
     * @return mixed
     */
    public function getValueAttribute()
    {
        try {
            $value = unserialize($this->meta_value);

            return $value === false && $this->meta_value !== false ?
                $this->meta_value :
                $value;
        } catch (Exception $ex) {
            return $this->meta_value;
        }
    }

    /**
     * @param array $models
     * @return MetaCollection
     */
    public function newCollection(array $models = [])
    {
        return new MetaCollection($models);
    }

    /**
     * Perform a createOrUpdate operation on the meta,
     * making sure $meta_key is unique for this object_id
     *
     * @return Meta
     */
    public static function updateSingle(string $meta_key, string $meta_value, int $object_id)
    {
        $object_col = static::reflectObjectColumnName();

        return static::updateOrCreate(
            [$object_col => $object_id, 'meta_key' => $meta_key],
            ['meta_value' => $meta_value]
        );
    }


    /**
     * Perform a createOrUpdate operation on the meta.
     *
     * @return Meta
     */
    public static function reflectObjectColumnName()
    {
        $reflect = new ReflectionClass(static::class);
        return strtolower(str_replace('Meta', '_id', $reflect->getShortName()));
    }
}
