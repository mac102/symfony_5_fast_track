<?php

namespace App\Tests\Controller;

use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
//use Symfony\Component\Panther\PantherTestCase;

class ConferenceControllerTest extends WebTestCase
//class ConferenceControllerTest extends PantherTestCase
{
    public function testIndex()
    {
        $client = static::createClient();
        //$client = static::createPantherClient(['external_base_uri' => $_SERVER['SYMFONY_PROJECT_DEFAULT_ROUTE_URL']]);
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
            'comment_form[email]' => $email = 'mac@mac.pl',
            'comment_form[photo]' => dirname(__DIR__, 2) . '/public/images/under-construction.gif',
        ]);

        // simulate comment validation
        $comment = self::$container->get(CommentRepository::class)->findOneByEmail($email);
        $comment->setState('published');
        self::$container->get(EntityManagerInterface::class)->flush();

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
