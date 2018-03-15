<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description', 'type', 'file',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['url'];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];

    /**
     * Geneates the full image url.
     *
     * @return  string
     */
    public function getUrlAttribute()
    {
        return env('APP_SITE') . 'c/' . $this->file . '.' . $this->type;
    }
}
