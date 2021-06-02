<?php 

declare(strict_types=1);

namespace App\Tests\Unit\Service;

use App\Entity\Question;
use App\Entity\QuestionHistory;
use App\Service\CsvExporter;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class CsvExporterTest extends KernelTestCase
{
    private string $headers;
    private string $firstRow;

    public function setUp(): void
    {
        self::bootKernel();
        $container = self::$kernel->getContainer();
        $csvExporter = $container->get(CsvExporter::class);

        $history = [
            (new QuestionHistory)
                ->setTitle('Question 1')
                ->setStatus(Question::STATUS_DRAFT)
                ->setCreatedAt(new DateTime)
        ];
        $csv = explode("\n", $csvExporter->export($history));

        $this->headers = array_shift($csv);
        $this->firstRow = array_shift($csv);
    }

    public function testContainsExpectedColumns(): void
    {
        $this->assertNotNull($this->headers);

        $headers = str_getcsv($this->headers);
        $this->assertEquals(3, count($headers));
    }

    public function testContainsExpectedData(): void
    {
        $this->assertNotNull($this->firstRow);
        
        $row = str_getcsv($this->firstRow);

        $this->assertEquals('Question 1', $row[0]);
        $this->assertEquals(Question::STATUS_DRAFT, $row[1]);
        $this->assertNotFalse(
            DateTime::createFromFormat(CsvExporter::DATETIME_FORMAT, $row[2]),
            'Wrong datetime format for createdAt'
        );
    }
}