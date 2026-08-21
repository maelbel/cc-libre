<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class AdminAccessTest extends WebTestCase
{
    public function testAdminAreaRedirectsAnonymousUsersToLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/cinemas');

        self::assertResponseRedirects('/admin/login');
    }

    public function testAdminAreaIsReachableForAnAuthenticatedAdmin(): void
    {
        $client = static::createClient();

        $admin = new User();
        $admin->setEmail('admin.test@cinegroupe.local');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword('irrelevant-already-hashed-value');

        $client->loginUser($admin);
        $client->request('GET', '/admin/cinemas');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Cinemas');
    }

    public function testLoginPageIsPubliclyAccessible(): void
    {
        $client = static::createClient();
        $client->request('GET', '/admin/login');

        self::assertResponseIsSuccessful();
    }
}
