<?php


namespace App\Service;


class NotificationSystemType extends AbstractNotification
{

    /**
     * @param array $post
     */
    public function setMeta(array $post): void
    {
        $priority = filter_var($post['priority'], FILTER_SANITIZE_NUMBER_INT);
        $this->notification->setMeta(['priority' => (int)$priority]);
    }

}