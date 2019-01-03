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

        /**
         * Logika pobierania Notyfikacji zawarta bezpośrednio w kontrolerze
         * - brak DI serwisu/managera do Notyfikacji
         * - brak serializera, budowanie zwrotki za pomocą foreach
         */
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
            /**
             * 'System' - powinien być CONST lub ENUM
             */
            if ($notification->getType()->getLabel() === 'System') {
                $meta = $notification->getMeta();
                $tmp['priority'] = $meta['priority'];
            }

            $arr['notifications'][] = $tmp;
        }

        /**
         * Zwrotka mogłaby być abstrakcyjnym Response'm
         */
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


        /**
         * Całość logiki filtrowania zawarta w kontrolerze.
         * - walidacja powinna być przeniesiona do zewnętrznego serwisu wstrzykniętego za pomoca DI do kontrolera
         * - logika wyciągania encji z requestu powinna być przesunięta do osobnych serwisów, kontroler zajmuje się wszystkim
         */
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

        /**
         * Tworzenie notyfikacji powinno być w Fabryce, nie w kontrolerze
         */
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


        /**
         * Brak wstrzykniętego serwisu do Notyfikacji
         */
        $repository = $this->getDoctrine()->getRepository(Notification::class);
        $notification = $repository->find($id);

        if (!$notification) {
            $arr['success'] = false;
            $arr['exception'] = 'No notification found for id '.$id;

            return $this->json($arr);
        }

        /**
         * Brak serializera
         */
        $tmp = [
            'id' => $notification->getId(),
            'title' => $notification->getTitle(),
            'description' => $notification->getDescription(),
            'sender' => $notification->getSender()->getId(),
            'recipient' => $notification->getRecipient()->getId(),
            'context' => $notification->getContext()->getId(),
            'status' => $notification->getStatus(),
        ];

        /**
         * Pozostawione TODO w kodzie
         */
        // todo: move to service
        if ($notification->getType()->getSlug() === 'system') {
            $meta = $notification->getMeta();
            $tmp['priority'] = $meta['priority'];
        }

        $arr['notifications'][] = $tmp;

        return $this->json($arr);
    }


    /**
     * Dwa poniższe route'y mogłyby być zebrane w jeden, nadmiarowy kod
     */
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
