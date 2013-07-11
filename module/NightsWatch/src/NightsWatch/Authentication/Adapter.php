<?php

namespace NightsWatch\Authentication;

use Doctrine\ORM\EntityManager;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\Bcrypt;

class Adapter implements AdapterInterface
{
    /** @var EntityManager */
    private $entityManager;
    private $username;
    private $password;

    public function __construct($username, $password, EntityManager $entityManager)
    {
        $this->username = $username;
        $this->password = $password;
        $this->entityManager = $entityManager;
    }


    public function authenticate()
    {
        /** @var \NightsWatch\Entity\User $user */
        $user = $this->entityManager->getRepository('User')->findOneBy(['username' => $this->username]);
        $bcrypt = new Bcrypt();

        if (is_null($user)) {
            return Result::FAILURE_IDENTITY_NOT_FOUND;
        } elseif (!$bcrypt->verify($this->password, $user->password)) {
            return Result::FAILURE_CREDENTIAL_INVALID;
        } else {
            return Result::SUCCESS;
        }
    }
}