<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class ParserNews
 *
 * @property          $id
 *
 * @property string   $title
 * @property string   $description
 * @property string   $text
 * @property string   $uri
 *
 * @property bool     $is_viewed
 * @property bool     $is_archived
 *
 * @property \DateTime $viewed_at
 * @property \DateTime $source_created_at
 *
 * @property \DateTime $created_at
 * @property \DateTime $updated_at
 *
 * @property integer  $user_id
 * @property User   $user
 *
 */
class ParserNews extends Model
{
    protected $table = 'parser_news';
    protected $fillable = [
        'title', 'description', 'text', 'uri', 'is_viewed', 'is_archived', 'viewed_at', 'source_created_at'
    ];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }
}
