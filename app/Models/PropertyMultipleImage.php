<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMultipleImage extends Model
{
    protected $fillable = ['property_id', 'image'];

    // Relationship with Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
