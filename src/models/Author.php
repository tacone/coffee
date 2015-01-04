<?php
namespace Tacone\Coffee\Demo\Models;
/**
 * Author
 */
class Author extends \Eloquent
{

    protected $table = 'demo_users';

    protected $appends = array('fullname');

    public function articles()
    {
        return $this->hasMany('\Tacone\Coffee\Demo\Models\Article');
    }

    public function comments()
    {
        return $this->hasMany('\Tacone\Coffee\Demo\Models\Comment');
    }

    public function getFullnameAttribute($value)
    {
        return $this->firstname ." ". $this->lastname;
    }

}
