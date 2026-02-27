<?php

declare(strict_types=1);

namespace App\Quiz\Application\Service\Interfaces;

use App\Quiz\Domain\Entity\Quiz;
use App\User\Domain\Entity\User;

interface QuizAccessServiceInterface
{
    public function isAdminLike(User $user): bool;

    public function canManageQuiz(User $user, Quiz $quiz): bool;
}
