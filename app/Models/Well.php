<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Well extends Model {
    protected $fillable = ['rig_id', 'name'];

    public function rig(): BelongsTo { return $this->belongsTo(Rig::class); }
    public function dailyLogs(): HasMany { return $this->hasMany(DailyLog::class); }
}
