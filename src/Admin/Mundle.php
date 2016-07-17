<?php
namespace Friparia\Admin;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Fluent;

abstract class Mundle extends Model{
    protected $_name = "";

    /**
     * List page configurations
     */
    protected $_unlistable = [];
    protected $_filterable = [];
    protected $_switchable = [];


    /**
     * Create or edit page  configurations
     */
    protected $_uncreatable = [];
    protected $_uneditable = [];


    protected $_unshowable = [];


    protected $guarded = [];

}
