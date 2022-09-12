<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class AbstractControllerTest extends WebTestCase
{
    protected function login(KernelBrowser $client, string $username = 'admin@email.test')
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser       = $userRepository->findOneByEmail($username);
        $client->loginUser($testUser);
    }
}
