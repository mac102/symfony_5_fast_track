<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ConferenceControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Give your feedback');
    }

    public function testConferencePage()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // weryfikacja, czy są dwa nagłówki h4
        $this->assertCount(2, $crawler->filter('h4'));

        $client->clickLink('View');
        // alternatywnie dla powyższego: $client->click($crawler->filter('h4 + p a')->link());

        $this->assertPageTitleContains('Wawa');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h2', 'Wawa 2018');
        $this->assertSelectorExists('div:contains("There are 1 comments")');
    }
}
