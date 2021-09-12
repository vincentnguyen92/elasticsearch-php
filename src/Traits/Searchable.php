<?php

namespace Okxe\Elasticsearch\Traits;

/**
 * The trait class for fixing a bug which increment and decrement not trigger
 * with saved method when using, this was be confirmed by Taylor Otwell 
 * and fixed in Laravel version 8.
 * https://github.com/laravel/framework/issues/32567#issuecomment-627424310
 * When you want to using the increment method or decrease method,
 * please attach this trait to the Model.
 * 
 * @author Vincent Nguyen <vannguyen@okxe.vn>
 */
trait Searchable
{
    /**
     * Increase a value to column field in database
     *
     * @param string $column
     * @param integer $amount
     * @param array $extra
     * @return void
     */
    public function increment($column, $amount = 1, array $extra = [])
    {
        $this->$column = $this->$column + $amount;

        $this->save();
    }

    /**
     * Decrease a value to column field in database
     *
     * @param string $column
     * @param integer $amount
     * @param array $extra
     * @return void
     */
    public function decrement($column, $amount = 1, array $extra = [])
    {
        $this->$column = $this->$column - $amount;

        $this->save();
    }
}
