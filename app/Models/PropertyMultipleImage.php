<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PropertyMultipleImage extends Model
{
    protected $fillable = ['property_id', 'image'];

    public function getImageAttribute($value){
        if(request()->is('api/*') && request()->isMethod('GET')){
            if ($value) {
                return asset($value);
            }
        }
        return $value ?? null;
    }

    // Relationship with Property
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
