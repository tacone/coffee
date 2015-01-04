<?php
namespace Tacone\Coffee\Demo\Models;
/**
 * Category
 */
class Category extends \Eloquent
{

    protected $table = 'demo_categories';

    public function articles()
    {
        return $this->belongsToMany('\Tacone\Coffee\Demo\Models\Article', 'demo_article_category', 'category_id','article_id');
    }

    public function parent()
    {
        return $this->belongsTo('\Tacone\Coffee\Demo\Models\Category', 'parent_id');
    }

    public function childrens()
    {
        return $this->hasMany('\Tacone\Coffee\Demo\Models\Category', 'parent_id');
    }
}
