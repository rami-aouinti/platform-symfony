<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260630120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add blog posts, tags, comments and task/task_request link tables.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql("CREATE TABLE blog_post (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', owner_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', title VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, excerpt LONGTEXT DEFAULT NULL, content LONGTEXT NOT NULL, status VARCHAR(64) NOT NULL, published_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_blog_post_owner_id (owner_id), INDEX idx_blog_post_status (status), UNIQUE INDEX uq_blog_post_slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE blog_tag (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', name VARCHAR(128) NOT NULL, slug VARCHAR(128) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', UNIQUE INDEX uq_blog_tag_name (name), UNIQUE INDEX uq_blog_tag_slug (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE blog_post_tag (blog_post_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', blog_tag_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', INDEX IDX_9BC66DAFEC32800F (blog_post_id), INDEX IDX_9BC66DAFC387F124 (blog_tag_id), PRIMARY KEY(blog_post_id, blog_tag_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE blog_comment (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', post_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', author_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', content LONGTEXT NOT NULL, reference_type VARCHAR(64) DEFAULT NULL, reference_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_blog_comment_post_id (post_id), INDEX idx_blog_comment_reference_type (reference_type), INDEX idx_blog_comment_reference_id (reference_id), INDEX IDX_C5A89A41235A57D (author_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql("CREATE TABLE blog_post_link (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', post_id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', task_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', task_request_id BINARY(16) DEFAULT NULL COMMENT '(DC2Type:uuid_binary_ordered_time)', reference_type VARCHAR(64) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', updated_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX idx_blog_post_link_post_id (post_id), INDEX idx_blog_post_link_task_id (task_id), INDEX idx_blog_post_link_task_request_id (task_request_id), UNIQUE INDEX uq_blog_post_link_post_task (post_id, task_id), UNIQUE INDEX uq_blog_post_link_post_request (post_id, task_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        $this->addSql('ALTER TABLE blog_post ADD CONSTRAINT FK_BA6F95D17E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_post_tag ADD CONSTRAINT FK_9BC66DAFEC32800F FOREIGN KEY (blog_post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_tag ADD CONSTRAINT FK_9BC66DAFC387F124 FOREIGN KEY (blog_tag_id) REFERENCES blog_tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_C5A89A44B89032C FOREIGN KEY (post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_comment ADD CONSTRAINT FK_C5A89A41235A57D FOREIGN KEY (author_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE blog_post_link ADD CONSTRAINT FK_AAFD7A264B89032C FOREIGN KEY (post_id) REFERENCES blog_post (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_link ADD CONSTRAINT FK_AAFD7A268DB60186 FOREIGN KEY (task_id) REFERENCES task (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE blog_post_link ADD CONSTRAINT FK_AAFD7A26F5B7AF75 FOREIGN KEY (task_request_id) REFERENCES task_request (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE blog_post DROP FOREIGN KEY FK_BA6F95D17E3C61F9');
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_C5A89A44B89032C');
        $this->addSql('ALTER TABLE blog_comment DROP FOREIGN KEY FK_C5A89A41235A57D');
        $this->addSql('ALTER TABLE blog_post_link DROP FOREIGN KEY FK_AAFD7A264B89032C');
        $this->addSql('ALTER TABLE blog_post_link DROP FOREIGN KEY FK_AAFD7A268DB60186');
        $this->addSql('ALTER TABLE blog_post_link DROP FOREIGN KEY FK_AAFD7A26F5B7AF75');
        $this->addSql('ALTER TABLE blog_post_tag DROP FOREIGN KEY FK_9BC66DAFEC32800F');
        $this->addSql('ALTER TABLE blog_post_tag DROP FOREIGN KEY FK_9BC66DAFC387F124');
        $this->addSql('DROP TABLE blog_comment');
        $this->addSql('DROP TABLE blog_post_link');
        $this->addSql('DROP TABLE blog_post_tag');
        $this->addSql('DROP TABLE blog_post');
        $this->addSql('DROP TABLE blog_tag');
    }
}
