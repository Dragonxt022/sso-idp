<?php

namespace App\Listeners;

use App\Events\UserActionEvent;
use App\Models\Audit;

class LogUserAction
{
    public function handle(UserActionEvent $event): void
    {
        Audit::create([
            'user_id'    => $event->userId,
            'action'     => $event->action,
            'description'=> $event->description,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'city'       => $event->city,
        ]);
    }
}
