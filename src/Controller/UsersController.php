<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController
{

    /**
     * @Route("/users/{id}/notifications", name="userNotifications")
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function userNotifications($id)
    {

        $arr = [
            'notifications' => [],
            'success' => true,
        ];

        $repository = $this->getDoctrine()->getRepository(User::class);
        $user = $repository->find($id);

        if (!$user) {
            $arr['success'] = false;
            $arr['exception'] = 'No user found for id '.$id;

            return $this->json($arr);
        }

        foreach ($user->getNotifications() as $notification) {
            $tmp = [
                'id' => $notification->getId(),
                'title' => $notification->getTitle(),
                'description' => $notification->getDescription(),
                'sender' => $notification->getSender()->getId(),
                'recipient' => $notification->getRecipient()->getId(),
                'context' => $notification->getContext()->getId(),
                'status' => $notification->getStatus(),
            ];
            if ($notification->getType()->getLabel() === 'System') {
                $meta = $notification->getMeta();
                $tmp['priority'] = $meta['priority'];
            }

            $arr['notifications'][] = $tmp;
        }

        return $this->json($arr);
    }
}
