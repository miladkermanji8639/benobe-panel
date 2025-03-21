<?php
namespace App\Models;

use App\Models\Clinic;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zone extends Model
{
    use HasFactory;

    protected $table   = 'zone';
    protected $guarded = ['id'];

    public function sluggable(): array
    {
        return [
            'slug' => ['source' => 'name'],
        ];
    }

    public function doctors()
    {
        return $this->hasMany(Doctor::class, 'city_id');
    }

    public function clinics()
    {
        return $this->hasMany(Clinic::class, 'city_id');
    }

    public function children()
    {
        return $this->hasMany(Zone::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Zone::class, 'parent_id');
    }

    public function scopeProvinces($query)
    {
        return $query->where('level', 1);
    }

    public function scopeCities($query)
    {
        return $query->where('level', 2);
    }
}
