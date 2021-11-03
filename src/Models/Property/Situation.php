<?php

namespace Jeffpereira\RealEstate\Models\Property;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Jeffpereira\RealEstate\Models\Traits\SetSlug;
use Jeffpereira\RealEstate\Models\Traits\UsesUuid;
use Illuminate\Support\Str;
use Jeffpereira\RealEstate\Models\Traits\UpperAttributes;

class Situation extends Model
{
    use UsesUuid, SetSlug, UpperAttributes;

    protected $guarded = [];

    protected function generateSlug()
    {
        return Str::slug($this->name);
    }

    protected function attributesUpper(): array
    {
        return ['name'];
    }

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }
}
