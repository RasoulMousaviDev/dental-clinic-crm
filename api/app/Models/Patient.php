<?php

namespace App\Models;

use App\Casts\JDate;
use App\Models\Model as ModelsModel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Patient extends Model
{
    use HasFactory;

    public $fillable = [
        'firstname',
        'lastname',
        'birthday',
        'telephone',
        'gender',
        'province',
        'city',
        'telephone',
        'lead_source',
        'status',
        'desc'
    ];

    protected $casts = [
        'created_at' => JDate::class,
        'updated_at' => JDate::class,
    ];

    // protected $with = [
    //     'mobiles',
    //     'city:id,title',
    //     'province:id,title',
    //     'leadSource:id,title',
    //     'treatments:id,title',
    //     'status'
    // ];

    public function mobiles(): HasMany
    {
        return $this->hasMany(PatientMobile::class);
    }

    public function calls(): HasMany
    {
        return $this->hasMany(Call::class);
    }

    public function followUps(): HasMany
    {
        return $this->hasMany(FollowUp::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'patient', 'id');
    }

    public function treatments(): BelongsToMany
    {
        return $this->belongsToMany(Treatment::class, 'patient_treatments');
    }

    public function treatmentPlans(): HasMany
    {
        return $this->hasMany(TreatmentPlan::class, 'patient', 'id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function leadSource(): BelongsTo
    {
        return $this->belongsTo(LeadSource::class, 'lead_source');
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class, 'status');
    }

    public static function model()
    {
        return ModelsModel::firstWhere('name', Patient::class);
    }

    public function birthday(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value,
            set: fn($value) => date('Y/m/d', strtotime($value))
        );
    }
}
