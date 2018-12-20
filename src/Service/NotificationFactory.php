<?php


namespace App\Service;


class NotificationFactory
{

    /**
     * @param $type
     * @return mixed
     */
    public function create($type)
    {

        $class = 'App\Service\Notification'.ucfirst($type).'Type';

        return $notification = new $class();

    }
}