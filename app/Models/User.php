<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
     protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'extension',
        'lead_target',
        'incoming_visit_target',
        'popup_visit_target',  
        'telecalling_target',
        'theme',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];
 
    public function routeNotificationForMail()
    {
        return $this->email;
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function createdJobs()
    {
        return $this->hasMany(Job::class, 'created_by');
    }

    public function plannedJobs()
    {
        return $this->hasMany(Job::class, 'assigned_planner_id');
    }

    public function operatorJobs()
    {
        return $this->hasMany(Job::class, 'assigned_operator_id');
    }

    public function team()
    {
        return $this->hasOne(Team::class, 'planner_id');
    }

    public function memberOfTeams()
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    public function uploadedFiles()
    {
        return $this->hasMany(JobFile::class, 'uploaded_by');
    }

    public function comments()
    {
        return $this->hasMany(JobComment::class);
    }

    public function statusChanges()
    {
        return $this->hasMany(JobHistory::class, 'changed_by');
    }
    
}
