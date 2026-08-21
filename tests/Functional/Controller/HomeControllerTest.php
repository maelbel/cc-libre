<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class HomeControllerTest extends WebTestCase
{
    public function testHomepageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Nos cinemas');
    }

    public function testHomepageSearchFormIsPresentAndResponsive(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertCount(1, $crawler->filter('input[name="q"]'));
        self::assertCount(1, $crawler->filter('input[name="film"]'));
        // Bootstrap's responsive navbar toggler must be present for small screens.
        self::assertCount(1, $crawler->filter('.navbar-toggler'));
    }
}
