<?php


namespace App\Service;


class NotificationFactory
{

    /**
     * $type powinien być ENUM aby nie spowodować błędów
     */
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
