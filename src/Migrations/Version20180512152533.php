<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180512152533 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE user_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE fos_user_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE project ALTER user_id DROP NOT NULL');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES fos_user (user_id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2FB3D0EEA76ED395 ON project (user_id)');
        $this->addSql('ALTER INDEX uniq_8d93d64992fc23a8 RENAME TO UNIQ_957A647992FC23A8');
        $this->addSql('ALTER INDEX uniq_8d93d649a0d96fbf RENAME TO UNIQ_957A6479A0D96FBF');
        $this->addSql('ALTER INDEX uniq_8d93d649c05fb297 RENAME TO UNIQ_957A6479C05FB297');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE fos_user_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE user_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER INDEX uniq_957a6479c05fb297 RENAME TO uniq_8d93d649c05fb297');
        $this->addSql('ALTER INDEX uniq_957a647992fc23a8 RENAME TO uniq_8d93d64992fc23a8');
        $this->addSql('ALTER INDEX uniq_957a6479a0d96fbf RENAME TO uniq_8d93d649a0d96fbf');
        $this->addSql('ALTER TABLE project DROP CONSTRAINT FK_2FB3D0EEA76ED395');
        $this->addSql('DROP INDEX IDX_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE project ALTER user_id SET NOT NULL');
    }
}
