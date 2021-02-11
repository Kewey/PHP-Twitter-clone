<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210211125900 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tweet (id INT AUTO_INCREMENT NOT NULL, author_id INT NOT NULL, content VARCHAR(280) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_3D660A3BF675F31B (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tweet_user (tweet_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_4F48308D1041E39B (tweet_id), INDEX IDX_4F48308DA76ED395 (user_id), PRIMARY KEY(tweet_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tweet ADD CONSTRAINT FK_3D660A3BF675F31B FOREIGN KEY (author_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE tweet_user ADD CONSTRAINT FK_4F48308D1041E39B FOREIGN KEY (tweet_id) REFERENCES tweet (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tweet_user ADD CONSTRAINT FK_4F48308DA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tweet_user DROP FOREIGN KEY FK_4F48308D1041E39B');
        $this->addSql('ALTER TABLE tweet DROP FOREIGN KEY FK_3D660A3BF675F31B');
        $this->addSql('ALTER TABLE tweet_user DROP FOREIGN KEY FK_4F48308DA76ED395');
        $this->addSql('DROP TABLE tweet');
        $this->addSql('DROP TABLE tweet_user');
        $this->addSql('DROP TABLE `user`');
    }
}
