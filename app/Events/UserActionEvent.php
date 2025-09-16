<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use App\Models\User;

class UserActionEvent
{
    use SerializesModels;

    public $userId;
    public $action;
    public $description;
    public $ipAddress;
    public $userAgent;
    public $city;

    public function __construct(User $user, $action, $description = null, $ipAddress = null, $userAgent = null)
    {
        $this->userId     = $user->id;
        $this->action     = $action;
        $this->description= $description;
        $this->ipAddress  = $ipAddress;
        $this->userAgent  = $userAgent;
        $this->city       = $user->unidade?->cidade; // agora funciona 👈
    }
}
