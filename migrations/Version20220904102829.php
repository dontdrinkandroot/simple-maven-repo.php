<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\SqlitePlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20220904102829 extends AbstractMigration
{
    /**
     * {@inheritdoc}
     */
    public function getDescription(): string
    {
        return 'Initial Migration';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            !($this->connection->getDatabasePlatform() instanceof SqlitePlatform),
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql(
            'CREATE TABLE maven_repository (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, visible BOOLEAN NOT NULL)'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_B35522713EE4B093 ON maven_repository (short_name)');
        $this->addSql(
            'CREATE TABLE repository_read_users (maven_repository_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(maven_repository_id, user_id), CONSTRAINT FK_45002B828FC124BF FOREIGN KEY (maven_repository_id) REFERENCES maven_repository (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_45002B82A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('CREATE INDEX IDX_45002B828FC124BF ON repository_read_users (maven_repository_id)');
        $this->addSql('CREATE INDEX IDX_45002B82A76ED395 ON repository_read_users (user_id)');
        $this->addSql(
            'CREATE TABLE repository_write_users (maven_repository_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(maven_repository_id, user_id), CONSTRAINT FK_3BDF2DD78FC124BF FOREIGN KEY (maven_repository_id) REFERENCES maven_repository (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_3BDF2DD7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('CREATE INDEX IDX_3BDF2DD78FC124BF ON repository_write_users (maven_repository_id)');
        $this->addSql('CREATE INDEX IDX_3BDF2DD7A76ED395 ON repository_write_users (user_id)');
        $this->addSql(
            'CREATE TABLE maven_repository_group (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, short_name VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, visible BOOLEAN NOT NULL)'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A74CB5803EE4B093 ON maven_repository_group (short_name)');
        $this->addSql(
            'CREATE TABLE repository_group_read_users (maven_repository_group_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(maven_repository_group_id, user_id), CONSTRAINT FK_5D8D98F661AC8951 FOREIGN KEY (maven_repository_group_id) REFERENCES maven_repository_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_5D8D98F6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('CREATE INDEX IDX_5D8D98F661AC8951 ON repository_group_read_users (maven_repository_group_id)');
        $this->addSql('CREATE INDEX IDX_5D8D98F6A76ED395 ON repository_group_read_users (user_id)');
        $this->addSql(
            'CREATE TABLE repository_group_repositories (maven_repository_group_id INTEGER NOT NULL, maven_repository_id INTEGER NOT NULL, PRIMARY KEY(maven_repository_group_id, maven_repository_id), CONSTRAINT FK_2EF7564C61AC8951 FOREIGN KEY (maven_repository_group_id) REFERENCES maven_repository_group (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_2EF7564C8FC124BF FOREIGN KEY (maven_repository_id) REFERENCES maven_repository (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)'
        );
        $this->addSql('CREATE INDEX IDX_2EF7564C61AC8951 ON repository_group_repositories (maven_repository_group_id)');
        $this->addSql('CREATE INDEX IDX_2EF7564C8FC124BF ON repository_group_repositories (maven_repository_id)');
        $this->addSql(
            'CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, username_canonical VARCHAR(180) NOT NULL, email VARCHAR(180) NOT NULL, email_canonical VARCHAR(180) NOT NULL, enabled BOOLEAN NOT NULL, salt VARCHAR(255) DEFAULT NULL, password VARCHAR(255) NOT NULL, last_login DATETIME DEFAULT NULL, confirmation_token VARCHAR(180) DEFAULT NULL, password_requested_at DATETIME DEFAULT NULL, roles CLOB NOT NULL --(DC2Type:array)
        , created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL)'
        );
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64992FC23A8 ON user (username_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649A0D96FBF ON user (email_canonical)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649C05FB297 ON user (confirmation_token)');
    }

    /**
     * {@inheritdoc}
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            !($this->connection->getDatabasePlatform() instanceof SqlitePlatform),
            'Migration can only be executed safely on \'sqlite\'.'
        );

        $this->addSql('DROP TABLE maven_repository');
        $this->addSql('DROP TABLE repository_read_users');
        $this->addSql('DROP TABLE repository_write_users');
        $this->addSql('DROP TABLE maven_repository_group');
        $this->addSql('DROP TABLE repository_group_read_users');
        $this->addSql('DROP TABLE repository_group_repositories');
        $this->addSql('DROP TABLE user');
    }
}
