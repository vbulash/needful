<?php

namespace App\Notifications\e2s;

use App\Models\History;
use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class Accepted2CancelledNotification extends Asked2CancelledNotification
{
}
