<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
/**
 * Class ParserSource
 *
 * @property          $id
 *
 * @property          $type
 * @property          $uri
 * @property          $keywords
 * @property          $is_active
 *
 * @property          $user_id
 *
 * @property \DateTime $executed_at
 *
 * @property User   $user
 *
 */
class ParserSource extends Model
{
    protected $table = 'parser_sources';
    protected $fillable = ['source', 'is_active', 'user_id', 'uri', 'keywords', 'type'];
    protected $guarded = ['id'];

    public function user()
    {
        return $this->belongsTo('\App\Models\User', 'user_id');
    }

}
