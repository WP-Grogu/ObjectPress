<?php

namespace OP\Lib\WpEloquent\Plugins\Acf\Field;

use OP\Lib\WpEloquent\Plugins\Acf\FieldInterface;
use OP\Lib\WpEloquent\Model\Post;
use Illuminate\Support\Collection;

/**
 * Class Gallery.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class Gallery extends Image implements FieldInterface
{
    /**
     * @var array
     */
    protected $images = [];

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @param $field
     */
    public function process($field)
    {
        if ($ids = $this->fetchValue($field)) {
            $connection = $this->post->getConnectionName();

            $ids_ordered = implode(',', $ids);

            $attachments = Post::on($connection)->whereIn('ID', $ids)
                ->orderByRaw("FIELD(ID, $ids_ordered)")->get();

            $metaDataValues = $this->fetchMultipleMetadataValues($attachments);

            foreach ($attachments as $attachment) {
                if (array_key_exists($attachment->ID, $metaDataValues)) {
                    $image = new Image($this->post);
                    $image->fillFields($attachment);
                    $image->fillMetadataFields($metaDataValues[$attachment->ID]);
                    $this->images[] = $image;
                }
            }
        }
    }

    /**
     * @return Collection
     */
    public function get()
    {
        if (!$this->collection instanceof Collection) {
            $this->collection = new Collection($this->images);
        }

        return $this->collection;
    }
}
