<?php

namespace App\Service;


interface NotificationInterface
{
    public function send();
    public function markRead();
    public function markUnread();
}