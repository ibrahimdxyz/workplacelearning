<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Class fieldtype
 * @package App
 *
 * @property int $fieldtype_id
 * @property string $fieldtype
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fieldtype whereFieldtype_id($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fieldtype whereFieldtype($value)
 *
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fieldtype newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fieldtype newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Fieldtype query()
 */

class fieldtype extends Model
{
    protected $table = 'fieldtype';

    //public $timestamps = false;

    protected $primaryKey = 'fieldtype_id';

    // Default
    protected $fillable = [
        'fieldtype_id',
        'fieldtype'
    ];

    public function column(): HasMany
    {
        return $this->hasMany(Column::class, 'fieldtype_id', 'fieldtype_id');
    }


    // Relations for query builder
    public function getRelationships(): array
    {
        return [
            'column',
        ];
    }
}