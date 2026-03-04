<?php

declare(strict_types=1);

namespace App\Page\Infrastructure\DataFixtures\ORM;

use App\General\Domain\Rest\UuidHelper;
use App\Page\Domain\Entity\Contact;
use App\Tests\Utils\PhpUnitUtil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Override;

final class LoadPageContactData extends Fixture implements OrderedFixtureInterface
{
    #[Override]
    public function load(ObjectManager $manager): void
    {
        $rows = [
            ['PageContact-support', '7b000000-0000-1000-8000-000000000001', 'support@example.com', 'Support request', 'Bonjour, je souhaite obtenir de l’aide sur mon compte.', null],
            ['PageContact-sales', '7b000000-0000-1000-8000-000000000002', 'sales@example.com', 'Product information', 'Pouvez-vous me transmettre les informations de tarification ?', 'tpl-sales-contact'],
            ['PageContact-partnership', '7b000000-0000-1000-8000-000000000003', 'partners@example.com', 'Partnership inquiry', 'Nous souhaitons discuter d’un partenariat avec votre équipe.', 'tpl-partnership'],
        ];

        foreach ($rows as [$reference, $uuid, $email, $subject, $content, $templateId]) {
            $contact = (new Contact())
                ->setEmail($email)
                ->setSubject($subject)
                ->setContent($content)
                ->setTemplateId($templateId);

            PhpUnitUtil::setProperty('id', UuidHelper::fromString($uuid), $contact);
            $manager->persist($contact);
            $this->addReference($reference, $contact);
        }

        $manager->flush();
    }

    #[Override]
    public function getOrder(): int
    {
        return 13;
    }
}
