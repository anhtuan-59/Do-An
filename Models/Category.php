<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable=['title','slug','photo','status','is_parent','parent_id','added_by'];

    public function parent_info(){
        return $this->hasOne('App\Models\Category','id','parent_id');
    }
    public static function getAllCategory(){
        return  Category::orderBy('id','DESC')->with('parent_info')->paginate(10);
    }

    public static function shiftChild($cat_id){
        return Category::whereIn('id',$cat_id)->update(['is_parent'=>1]);
    }
    public static function getChildByParentID($id){
        return Category::where('parent_id',$id)->orderBy('id','ASC')->pluck('title','slug','id');
    }

    public function child_cat(){
        return $this->hasMany('App\Models\Category','parent_id','id')->where('status','1');
    }
    public static function getAllParentWithChild(){
        return Category::with('child_cat')->where('is_parent',1)->where('status','1')->orderBy('title','ASC')->limit(3)->get();
    }
    public function products(){
        return $this->hasMany('App\Models\Product','cat_id','id')->where('status','1');
    }
    public function sub_products(){
        return $this->hasMany('App\Models\Product','child_cat_id','id')->where('status','1');
    }
    public static function getProductByCat($slug){
        // dd($slug);
        return Category::with('products')->where('slug',$slug)->first();
        // return Product::where('cat_id',$id)->where('child_cat_id',null)->paginate(10);
    }
    public static function getProductBySubCat($slug){
        // return $slug;
        return Category::with('sub_products')->where('slug',$slug)->first();
    }
    public function scopeActive($query) {
        return $query->where('status', '1');
    }
    public function productCount() {
        return $this->products()->count();
    }

    public static function countActiveCategory(){
        $data=Category::where('status','1')->count();
        if($data){
            return $data;
        }
        return 0;
    }
}