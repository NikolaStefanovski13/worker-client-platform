<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Determine if the user is a worker.
     *
     * @return bool
     */
    public function isWorker()
    {
        return $this->user_type === 'worker';
    }

    /**
     * Determine if the user is a client.
     *
     * @return bool
     */
    public function isClient()
    {
        return $this->user_type === 'client';
    }

    /**
     * Get the user's profile.
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the user's skills.
     */
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills')
            ->withTimestamps();
    }

    /**
     * Get the user's service areas.
     */
    public function serviceAreas()
    {
        return $this->hasMany(ServiceArea::class);
    }

    /**
     * Get the jobs posted by the client.
     */
    public function postedJobs()
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    /**
     * Get the job applications submitted by the worker.
     */
    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'worker_id');
    }

    /**
     * Get the messages sent by the user.
     */
    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Get the messages received by the user.
     */
    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Get the reviews given by the user.
     */
    public function givenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }

    /**
     * Get the reviews received by the user.
     */
    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    /**
     * Get the user's notifications.
     */
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Get the user's average rating.
     *
     * @return float
     */
    public function getAverageRatingAttribute()
    {
        $reviews = $this->receivedReviews;
        if ($reviews->isEmpty()) {
            return 0;
        }

        return $reviews->avg('rating');
    }
}
