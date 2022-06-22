<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class CreateUserFromCommand
{

    /**
     * @var UserPasswordHasherInterface
     */
    private UserPasswordHasherInterface $userPasswordHasher;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
    }

    public function createUser($u_name, $u_email, $u_pass)
    {

        $u = new User();
        $u->setName($u_name);
        $u->setEmail($u_email);
        $u->setPassword($this->userPasswordHasher->hashPassword($u, $u_pass));

        $this->entityManager->persist($u);
        $this->entityManager->flush();

    }
}