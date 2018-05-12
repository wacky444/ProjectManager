<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180512144533 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE project_project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER TABLE project DROP id');
        $this->addSql('ALTER TABLE project ADD PRIMARY KEY (project_id)');
        $this->addSql('ALTER TABLE task DROP id');
        $this->addSql('ALTER TABLE task ADD PRIMARY KEY (task_id)');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN id TO user_id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP SEQUENCE project_project_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE task_task_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE user_user_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE project_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE task_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('DROP INDEX fos_user_pkey');
        $this->addSql('ALTER TABLE "user" RENAME COLUMN user_id TO id');
        $this->addSql('ALTER TABLE "user" ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX project_pkey');
        $this->addSql('ALTER TABLE project ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE project ADD PRIMARY KEY (id)');
        $this->addSql('DROP INDEX task_pkey');
        $this->addSql('ALTER TABLE task ADD id INT NOT NULL');
        $this->addSql('ALTER TABLE task ADD PRIMARY KEY (id)');
    }
}
