<?php

declare(strict_types=1);

namespace App\Tests\Application\Resume\Transport\Controller\Api\V1;

use App\General\Domain\Utils\JSON;
use App\Tests\TestCase\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ProfileResumeComponentsControllerTest extends WebTestCase
{
    /**
     * @throws Throwable
     */
    public function testLoggedUserCanCreateResumeEducationExperienceAndSkill(): void
    {
        $client = $this->getTestClient('john-user', 'password-user');
        $baseResumeUrl = self::API_URL_PREFIX . '/v1/me/profile/resumes';

        $client->request('POST', $baseResumeUrl, content: JSON::encode([
            'title' => 'Profile Resume Components',
            'summary' => 'Summary long enough for component creation tests.',
            'isPublic' => false,
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $resume = JSON::decode((string)$client->getResponse()->getContent(), true);
        $resumeId = (string)$resume['id'];

        $client->request('POST', self::API_URL_PREFIX . '/v1/me/profile/resumes/' . $resumeId . '/educations', content: JSON::encode([
            'schoolName' => 'Tech University',
            'degree' => 'MSc Computer Science',
            'level' => 'master',
            'startDate' => '2020-09-01T00:00:00+00:00',
            'endDate' => '2022-06-30T00:00:00+00:00',
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $client->request('POST', self::API_URL_PREFIX . '/v1/me/profile/resumes/' . $resumeId . '/experiences', content: JSON::encode([
            'title' => 'Backend Developer',
            'companyName' => 'ACME',
            'employmentType' => 'full_time',
            'startDate' => '2022-07-01T00:00:00+00:00',
            'isCurrent' => true,
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());

        $client->request('POST', self::API_URL_PREFIX . '/v1/me/profile/resumes/' . $resumeId . '/skills', content: JSON::encode([
            'name' => 'Symfony',
            'level' => 'advanced',
            'yearsExperience' => 4,
        ]));
        self::assertSame(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }
}
