<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180512142858 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE fos_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE project (id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, project_id INT NOT NULL, user_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE task (id INT NOT NULL, task_id INT NOT NULL, project_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER INDEX uniq_957a647992fc23a8 RENAME TO UNIQ_8D93D64992FC23A8');
        $this->addSql('ALTER INDEX uniq_957a6479a0d96fbf RENAME TO UNIQ_8D93D649A0D96FBF');
        $this->addSql('ALTER INDEX uniq_957a6479c05fb297 RENAME TO UNIQ_8D93D649C05FB297');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE fos_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE task');
        $this->addSql('ALTER INDEX uniq_8d93d649c05fb297 RENAME TO uniq_957a6479c05fb297');
        $this->addSql('ALTER INDEX uniq_8d93d64992fc23a8 RENAME TO uniq_957a647992fc23a8');
        $this->addSql('ALTER INDEX uniq_8d93d649a0d96fbf RENAME TO uniq_957a6479a0d96fbf');
    }
}
