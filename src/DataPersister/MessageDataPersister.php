<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\Message;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MessageDataPersister implements DataPersisterInterface
{
    private $entityManager;
    private $httpClient;
    private $security;

    public function __construct(EntityManagerInterface $manager,
                               HttpClientInterface $client, Security $security)
    {
        $this->entityManager = $manager;
        $this->httpClient = $client;
        $this->security = $security;
    }

    /**
     * @inheritDoc
     */
    public function supports($data): bool
    {
        return $data instanceof Message;
    }

    /**
     * @inheritDoc
     * @param $data Message
     * @throws TransportExceptionInterface
     */
    public function persist($data)
    {
        $this->httpClient->request('POST',
            'https://ender.onrender.com/messages/create',
            ['headers' =>
                ['Authorization'=>"Bearer {$_ENV['SMS_GATEWAY_TOKEN']}"],
                'json' =>
                ['phone' => $data->getContacts()[0]->getPhone(),
                    'message_body' => $data->getContent()]
            ]);
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