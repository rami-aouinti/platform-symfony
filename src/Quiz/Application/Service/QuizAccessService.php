<?php

declare(strict_types=1);

namespace App\Quiz\Application\Service;

use App\Quiz\Application\Service\Interfaces\QuizAccessServiceInterface;
use App\Quiz\Domain\Entity\Quiz;
use App\User\Domain\Entity\User;

use function in_array;

/**
 * QuizAccessService.
 *
 * @package App\Quiz\Application\Service
 * @author Dmitry Kravtsov <dmytro.kravtsov@systemsdk.com>
 */
class QuizAccessService implements QuizAccessServiceInterface
{
    public function isAdminLike(User $user): bool
    {
        return in_array('ROLE_ROOT', $user->getRoles(), true) || in_array('ROLE_ADMIN', $user->getRoles(), true);
    }

    public function canManageQuiz(User $user, Quiz $quiz): bool
    {
        return $this->isAdminLike($user) || $quiz->getOwner()?->getId() === $user->getId();
    }
}
