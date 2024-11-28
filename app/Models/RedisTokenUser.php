<?php

namespace App\Models;

use ArrayAccess;
use Illuminate\Contracts\Auth\Authenticatable;

class RedisTokenUser implements Authenticatable, ArrayAccess
{
    private $attributes = array();

    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;

        //将attributes转换为属性供外部访问
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * @return string
     */
    public function getAuthIdentifierName()
    {
        // Return the name of unique identifier for the user (e.g. "id")
        return 'id';
    }

    /**
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // Return the unique identifier for the user (e.g. their ID, 123)
        $identifier_name = $this->getAuthIdentifierName();
        return $this->attributes[$identifier_name];
    }

    /**
     * @return string
     */
    public function getAuthPassword()
    {
        // Returns the (hashed) password for the user
    }

    /**
     * @return string
     */
    public function getRememberToken()
    {
        // Return the token used for the "remember me" functionality
    }

    /**
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // Save a new token user for the "remember me" functionality
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        // Return the name of the column / attribute used to store the "remember me" token
    }

    //实现ArrayAccess允许像数组字段一样访问

    public function offsetExists($offset) {
        return isset($this->attributes[$offset]);
    }

    public function offsetGet($offset) {
        return $this->attributes[$offset] ?? null;
    }

    public function offsetSet($offset, $value) {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->attributes[$offset]);
    }
}
