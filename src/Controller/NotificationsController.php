<?php

namespace App\Controller;

use App\Entity\Message;
use App\Entity\Notification;
use App\Entity\NotificationType;
use App\Entity\User;
use App\Service\NotificationFactory;
use InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationsController extends AbstractController
{
    /**
     * @Route("/notifications", name="showNotifications", methods={"GET","HEAD"})
     */
    public function showNotifications()
    {
        $arr = [
            'notifications' => [],
            'success' => true,
        ];

        $repository = $this->getDoctrine()->getRepository(Notification::class);
        $notifications = $repository->findAll();
        foreach ($notifications as $notification) {
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


    /**
     * @Route("/notifications", name="createNotification", methods={"POST"})
     * @param Request            $request
     * @param ValidatorInterface $validator
     * @return Response
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function send(Request $request, ValidatorInterface $validator)
    {

        $post = $request->request->all();

        $entityManager = $this->getDoctrine()->getManager();

        $type = filter_var($post['type'], FILTER_SANITIZE_STRING);
        $notificationType = $entityManager->getRepository(NotificationType::class)->findOneBySlug($type);
        if (!$notificationType) {
            throw new InvalidArgumentException('NotificationType '.$type.' not found.');
        }


        if ($type == 'system') {
            $sender = $entityManager->getRepository(User::class)->findOneBy(['attribute' => 2]);
        } else {
            $senderId = filter_var($post['sender'], FILTER_SANITIZE_NUMBER_INT);
            $sender = $entityManager->getRepository(User::class)->find($senderId);
            if (!$sender) {
                throw new InvalidArgumentException('User '.$senderId.' not found.');
            }
        }

        $recipientId = filter_var($post['recipient'], FILTER_SANITIZE_NUMBER_INT);
        $recipient = $entityManager->getRepository(User::class)->find($recipientId);
        if (!$recipient) {
            throw new InvalidArgumentException('User '.$recipientId.' not found.');
        }

        $contextId = filter_var($post['context'], FILTER_SANITIZE_NUMBER_INT);
        $context = $entityManager->getRepository(Message::class)->find($contextId);
        if (!$context) {
            throw new InvalidArgumentException('Message '.$contextId.' not found.');
        }


        $notificationService = (new NotificationFactory)->create($type);

        $notification = $notificationService->create();

        $notification
            ->setTitle($post['title'])
            ->setDescription($post['description'])
            ->setSender($sender)
            ->setRecipient($recipient)
            ->setContext($context)
            ->setType($notificationType);

        // deal with extra content
        $notificationService->setMeta($post);

        // validate according to type
        $hasErrors = $notificationService->validate($validator);
        if ($hasErrors) {
            throw new \Exception((string)$notificationService->getErrors());
        }

        $entityManager->persist($notification);
        $entityManager->flush();

        return $this->json(['success' => true], 201);
    }


    /**
     * @Route("/notifications/{id}", name="showNotification", methods={"GET"})
     */
    public function show($id)
    {

        $arr = [
            'notifications' => [],
            'success' => true,
        ];


        $repository = $this->getDoctrine()->getRepository(Notification::class);
        $notification = $repository->find($id);

        if (!$notification) {
            $arr['success'] = false;
            $arr['exception'] = 'No notification found for id '.$id;

            return $this->json($arr);
        }

        $tmp = [
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'description' => $notification->getDescription(),
            'sender' => $notification->getSender()->getId(),
            'recipient' => $notification->getRecipient()->getId(),
            'context' => $notification->getContext()->getId(),
            'status' => $notification->getStatus(),
        ];

        // todo: move to service
        if ($notification->getType()->getSlug() === 'system') {
            $meta = $notification->getMeta();
            $tmp['priority'] = $meta['priority'];
        }

        $arr['notifications'][] = $tmp;

        return $this->json($arr);
    }


    /**
     * @Route(
     *     "/notifications/{id}/mark/read",
     *     name="markRead",
     *     methods={"PUT"},
     *     requirements={"id"="\d+"}
     * )
     */
    public function markRead($id)
    {
        $arr = [
            'notifications' => [],
            'success' => true,
        ];

        $entityManager = $this->getDoctrine()->getManager();
        $notification = $entityManager->getRepository(Notification::class)->find($id);

        if (!$notification) {
            $arr['success'] = false;
            $arr['exception'] = 'No notification found for id '.$id;

            return $this->json($arr);
        }

        $notification->setStatus('read');
        $entityManager->flush();

        return $this->json(['success' => true], 201);
    }


    /**
     * @Route(
     *     "/notifications/{id}/mark/unread",
     *     name="markUnread",
     *     methods={"PUT"},
     *     requirements={"id"="\d+"}
     * )
     */
    public function markUnread($id)
    {
        $arr = [
            'notifications' => [],
            'success' => true,
        ];

        $entityManager = $this->getDoctrine()->getManager();
        $notification = $entityManager->getRepository(Notification::class)->find($id);

        if (!$notification) {
            $arr['success'] = false;
            $arr['exception'] = 'No notification found for id '.$id;

            return $this->json($arr);
        }

        $notification->setStatus('unread');
        $entityManager->flush();

        return $this->json(['success' => true], 201);
    }

}
