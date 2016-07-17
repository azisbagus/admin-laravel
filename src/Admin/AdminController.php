<?php
namespace Friparia\Admin;
use Illuminate\Routing\Controller as LaravelController;
use Illuminate\Http\Request;
class AdminController extends LaravelController
{
    public function dashboard(Request $request){
        return view('admin::layout');
    }
}
