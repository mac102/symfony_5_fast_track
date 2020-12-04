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

    public function testCommentSubmission()
    {
        $client = static::createClient();
        $client->request('GET', '/conference/wawa-2018');
        $client->submitForm('Submit', [
            'comment_form[author]' => 'Maciej',
            'comment_form[text]' => 'Some feedback from an automated functional test',
            'comment_form[email]' => 'mac@mac.pl',
            'comment_form[photo]' => dirname(__DIR__, 2) . '/public/images/under-construction.gif',
        ]);
        $this->assertResponseRedirects();
        $client->followRedirect();
        $this->assertSelectorExists('div:contains("There are 2 comments")');
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
