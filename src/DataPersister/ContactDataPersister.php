<?php

namespace App\DataPersister;

use App\Entity\Contact;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class ContactDataPersister implements \ApiPlatform\Core\DataPersister\DataPersisterInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $manager, Security $security)
    {
        $this->entityManager = $manager;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof Contact;
    }

    /**
     * @inheritDoc
     * @param $data Contact
     */
    public function persist($data)
    {
        /** @noinspection PhpParamsInspection */
        $data->setUser($this->security->getUser());
        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @inheritDoc
     */
    public function remove($data)
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}