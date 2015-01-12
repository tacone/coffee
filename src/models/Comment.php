<?php
namespace Tacone\Coffee\Demo\Models;

/**
 * Comment
 */
class Comment extends \Eloquent
{
    protected $table = 'demo_comments';

    public function article()
    {
        return $this->belongsTo('\Tacone\Coffee\Demo\Models\Article', 'article_id');
    }

    public function author()
    {
        return $this->belongsTo('\Tacone\Coffee\Demo\Models\Author', 'author_id');
    }
}
