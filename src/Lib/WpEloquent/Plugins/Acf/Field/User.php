<?php

namespace OP\Lib\WpEloquent\Plugins\Acf\Field;

use OP\Lib\WpEloquent\Plugins\Acf\FieldInterface;
use OP\Lib\WpEloquent\Model\Post;

/**
 * Class User.
 *
 * @author Junior Grossi <juniorgro@gmail.com>
 */
class User extends BasicField implements FieldInterface
{
    /**
     * @var \OP\Lib\WpEloquent\Model\User
     */
    protected $user;

    /**
     * @var \OP\Lib\WpEloquent\Model\User
     */
    protected $value;

    /**
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        parent::__construct($post);
        $this->user = new \OP\Lib\WpEloquent\Model\User();
        $this->user->setConnection($post->getConnectionName());
    }

    /**
     * @param string $fieldName
     */
    public function process($fieldName)
    {
        $userId = $this->fetchValue($fieldName);
        $this->value = $this->user->find($userId);
    }

    /**
     * @return \OP\Lib\WpEloquent\Model\User
     */
    public function get()
    {
        return $this->value;
    }
}
