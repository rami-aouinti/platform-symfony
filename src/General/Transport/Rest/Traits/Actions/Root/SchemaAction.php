<?php

declare(strict_types=1);

namespace App\General\Transport\Rest\Traits\Actions\Root;

use App\General\Transport\Rest\Traits\Methods\SchemaMethod;
use App\Role\Domain\Enum\Role;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Throwable;

trait SchemaAction
{
    use SchemaMethod;

    /**
     * @throws Throwable
     */
    #[Route(path: '/schema', methods: [Request::METHOD_GET])]
    #[IsGranted(Role::ROOT->value)]
    public function schemaAction(Request $request): Response
    {
        return $this->schemaMethod($request);
    }
}
