<?php

namespace App\Tests\Entity;

use App\Entity\PrivateMessage;
use App\Entity\Report;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class PrivateMessageTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $privateMessage = $this->getEntity();
        $this->assertTrue($privateMessage->getTitle() === $data['title']);
        $this->assertTrue($privateMessage->getContent() === $data['content']);
        $this->assertTrue($privateMessage->getAddressee()->getUsername() === $data['addressee']->getUsername());
        $this->assertTrue($privateMessage->getAuthor()->getUsername() === $data['author']->getUsername());
        $this->assertTrue($privateMessage->getCreated()->format("d/m/Y") === $data['created']->format("d/m/Y") );
    }

    public function testIsFalse(): void
    {
        $privateMessage = $this->getEntity();
        $this->assertFalse($privateMessage->getTitle() === 'False');
        $this->assertFalse($privateMessage->getContent() === 'False');
        $this->assertFalse($privateMessage->getAddressee() === 'False');
        $this->assertFalse($privateMessage->getAuthor() === 'False');
        $this->assertFalse($privateMessage->getCreated() === 'False');
    }

    public function testIEmpty(): void
    {
        $privateMessage = new PrivateMessage();
        $this->assertEmpty($privateMessage->getTitle());
        $this->assertEmpty($privateMessage->getContent());
        $this->assertEmpty($privateMessage->getAddressee());
        $this->assertEmpty($privateMessage->getAuthor());
        $this->assertEmpty($privateMessage->getCreated());
    }

    public function testAddReport(): void
    {
        $privateMessage = $this->getEntity();
        $report = (new Report())->setMessage("Demo");
        $privateMessage->addReport($report);
        $this->assertTrue($report === $privateMessage->getReports()[0]);
    }

    public function testRemoveReport(): void
    {
        $privateMessage = $this->getEntity();
        $report = (new Report())->setMessage("Demo");
        $privateMessage->addReport($report);
        $privateMessage->removeReport($report);
        $this->assertTrue($privateMessage->getReports()->isEmpty());
    }


    private function getData(): array
    {
        return [
            'title' => 'Lorem',
            'content' => 'lorem lorem lorem lorem lorem',
            'created' => new DateTime('2021-10-14'),
            'author' => (new User())->setUsername("Ermina"),
            'addressee' => (new User())->setUsername("Avalia")
        ];
    }

    public function getEntity(): PrivateMessage
    {
        $data = $this->getData();
        return (new PrivateMessage())
            ->setTitle($data['title'])
            ->setContent($data['content'])
            ->setCreated($data['created'])
            ->setAuthor($data['author'])
            ->setAddressee($data['addressee']);
    }
}
