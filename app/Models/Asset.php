<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = ['user_id', 'name', 'type', 'value', 'description', 'source_type', 'source_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSourceAttribute()
    {
        if ($this->source_type === 'wallet') {
            return Wallet::find($this->source_id);
        } elseif ($this->source_type === 'asset') {
            return Asset::find($this->source_id);
        }
        return null;
    }
}
