<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210602093344 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_history ADD question_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE question_history ADD CONSTRAINT FK_2966A5561E27F6BF FOREIGN KEY (question_id) REFERENCES question (id)');
        $this->addSql('CREATE INDEX IDX_2966A5561E27F6BF ON question_history (question_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question_history DROP FOREIGN KEY FK_2966A5561E27F6BF');
        $this->addSql('DROP INDEX IDX_2966A5561E27F6BF ON question_history');
        $this->addSql('ALTER TABLE question_history DROP question_id');
    }
}
