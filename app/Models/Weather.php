<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Weather extends Model
{
    use HasFactory;

    protected $table = 'weathers';

    protected $fillable = [
        'city',
        'country',
        'temperature',
        'humidity',
        'pressure',
        'condition',
        'visibility',
        'collected_at',
    ];

    protected $casts = [
        'collected_at' => 'datetime',
    ];

    protected $dates = ['collected_at'];

    // Scopes for convenient querying by city, country, collected date and recent

    public function scopeByCity($query, $city)
    {
        return $query->where('city', $city);
    }

    public function scopeByCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByCollectedAt($query, $date)
    {
        return $query->whereDate('collected_at', $date);
    }
}
