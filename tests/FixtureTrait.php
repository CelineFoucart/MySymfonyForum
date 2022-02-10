<?php
namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\DataFixtures\TopicFixtures;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

trait FixtureTrait 
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;

    /**
     * Hydrates the test database
     * 
     * @return void
     */
    protected function makeFixture(): void
    {
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([AppFixtures::class, TopicFixtures::class], ORMPurger::PURGE_MODE_TRUNCATE);
    }
}