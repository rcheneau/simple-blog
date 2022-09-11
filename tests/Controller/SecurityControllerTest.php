<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/en/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Login');

        $form = $crawler->filter('[type=submit]')->form();

        $form['email'] = 'admin@email.test';
        $form['password'] = 'password';

        $client->submit($form);

        $this->assertResponseStatusCodeSame(302);

        $client->request('GET', '/en/logout');
        $this->assertResponseStatusCodeSame(302);
        $crawler = $client->followRedirect();
    }
}