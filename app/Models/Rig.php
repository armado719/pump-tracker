<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rig extends Model {
    protected $fillable = ['name', 'location', 'manager'];

    public function wells(): HasMany { return $this->hasMany(Well::class); }
    public function pumps(): HasMany { return $this->hasMany(Pump::class); }
    public function users(): HasMany { return $this->hasMany(User::class); }
}
