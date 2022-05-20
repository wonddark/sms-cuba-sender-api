<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDataPersisteer implements DataPersisterInterface
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager,
                                UserPasswordHasherInterface $passwordHasher) {
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $passwordHasher;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof User;
    }

    /**
     * @inheritDoc
     * @param $data User
     */
    public function persist($data)
    {
        if ($data->getPlainPassword()) {
            $data->setPassword($this->userPasswordHasher->hashPassword($data,
                $data->getPlainPassword()));
            $data->eraseCredentials();
        }

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