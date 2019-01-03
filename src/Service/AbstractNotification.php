<?php

namespace App\Service;


use App\Entity\Notification;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractNotification implements NotificationInterface
{

    /**
     * @var Notification
     */
    protected $notification;
    protected $errors;

    /**
     * @return Notification
     */
    public function getNotification(): Notification
    {
        return $this->notification;
    }

    /**
     * @param Notification $notification
     * @return AbstractNotification
     */
    public function setNotification(Notification $notification): AbstractNotification
    {
        $this->notification = $notification;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $errors
     * @return AbstractNotification
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;

        return $this;
    }


    public function create()
    {
        $this->notification = new Notification();
        return $this->notification;
    }

    public function validate(ValidatorInterface $validator)
    {
        $this->errors = $validator->validate($this->notification);

        return (bool)count($this->errors);
    }

    /**
     * Pozostawione TODO w kodzie, brak faktycznej implementacji
     */
    public function send()
    {
        // TODO: Implement send() method.

    }
    /**
     * Pozostawione TODO w kodzie, brak faktycznej implementacji
     */
    public function markRead()
    {
        // TODO: Implement markRead() method.
    }
    /**
     * Pozostawione TODO w kodzie, brak faktycznej implementacji
     */
    public function markUnread()
    {
        // TODO: Implement markUnread() method.
    }

}
