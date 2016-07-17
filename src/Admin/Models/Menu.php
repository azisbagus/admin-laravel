<?php
/**
 * Created by PhpStorm.
 * User: friparia
 * Date: 16/4/6
 * Time: 23:48
 */

namespace Friparia\Admin\Models;

use Friparia\Admin\Model;

class Menu extends Model
{
    /**
     *
     */
    public $timestamps = false;
    protected function construct()
    {
        $this->fields->integer('pid');
        $this->fields->string('name');
        $this->fields->string('url')->nullable();
        $this->fields->integer('weight');
    }

}
