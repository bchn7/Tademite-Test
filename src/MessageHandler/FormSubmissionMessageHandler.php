<?php
namespace App\MessageHandler;

use App\Message\FormSubmissionMessage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class FormSubmissionMessageHandler
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(FormSubmissionMessage $message)
    {
        $user = new User();
        $user->setFirstName($message->getFirstName());
        $user->setLastName($message->getLastName());
        $user->setAttachmentPath($message->getAttachmentPath());

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}