<?php

namespace App\Tests\Controller;

class UserControllerTest extends AbstractControllerTest
{
    public function testLogin()
    {
        $client = static::createClient();

        $this->login($client);
        $client->request('GET', '/en/user/profile');

        $this->assertResponseIsSuccessful();
    }
}
