<?php

namespace App\Tests\Entity;

use App\Entity\Report;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ReportTest extends TestCase
{
    public function testIsTrue(): void
    {
        $data = $this->getData();
        $report = $this->getEntity();
        $this->assertTrue($report->getMessage() === $data['message']);
        $this->assertTrue($report->getType() === $data['type']);
        $this->assertTrue($report->getAuthor()->getUsername() === $data['author']->getUsername());
    }

    public function testIsFalse(): void
    {
        $report = $this->getEntity();
        $this->assertFalse($report->getMessage() === 'False');
        $this->assertFalse($report->getType() === 'False');
        $this->assertFalse($report->getAuthor() === 'False');
    }

    public function testIsEmpty(): void
    {
        $report = new Report();
        $this->assertEmpty($report->getMessage());
        $this->assertEmpty($report->getType());
        $this->assertEmpty($report->getAuthor());
    }

    private function getData(): array
    {
        return [
            'message' => 'Lorem Lorem Lorem',
            'author' => (new User())->setUsername('Ermina'),
            'type' => "post",
        ];
    }

    private function getEntity(): Report
    {
        $data =$this->getData();
        return (new Report())
            ->setMessage($data['message'])
            ->setAuthor($data['author'])
            ->setType($data['type']);
    }
}
