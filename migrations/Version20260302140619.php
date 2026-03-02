<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260302140619 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL, status VARCHAR(50) NOT NULL, qr_code VARCHAR(155) NOT NULL, user_id INT NOT NULL, INDEX IDX_42C84955A76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE reservation_item (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, reservation_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_922E876B83297E7 (reservation_id), INDEX IDX_922E8764584665A (product_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE reservation_item ADD CONSTRAINT FK_922E876B83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)');
        $this->addSql('ALTER TABLE reservation_item ADD CONSTRAINT FK_922E8764584665A FOREIGN KEY (product_id) REFERENCES product (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955A76ED395');
        $this->addSql('ALTER TABLE reservation_item DROP FOREIGN KEY FK_922E876B83297E7');
        $this->addSql('ALTER TABLE reservation_item DROP FOREIGN KEY FK_922E8764584665A');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE reservation_item');
    }
}
