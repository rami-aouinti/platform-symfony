<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Page\Domain\Entity\Faq;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadPageFaqData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['PageFaq-account', '7c000000-0000-1000-8000-000000000001', 'Comment créer un compte ?', 'Cliquez sur Inscription puis validez votre email pour activer le compte.', 1],
            ['PageFaq-reset-password', '7c000000-0000-1000-8000-000000000002', 'Comment réinitialiser mon mot de passe ?', 'Utilisez le lien Mot de passe oublié depuis la page de connexion.', 2],
            ['PageFaq-contact-support', '7c000000-0000-1000-8000-000000000003', 'Comment contacter le support ?', 'Accédez à la page Contact et envoyez votre demande avec les détails du problème.', 3],
        ];

        foreach ($rows as [$reference, $uuid, $name, $description, $order]) {
            $faq = (new Faq())
                ->setName($name)
                ->setDescription($description)
                ->setOrder($order);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $faq);
            $manager->persist($faq);
            $this->addReference($reference, $faq);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 13;
    }
}
