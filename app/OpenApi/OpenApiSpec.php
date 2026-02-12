<?php

declare(strict_types=1);

namespace App\OpenApi;

use OpenApi\Attributes as OA;

#[OA\OpenApi]
#[OA\Info(version: '1.0.0', title: 'Arrivo API')]
#[OA\Server(url: '/api', description: 'API Base URL')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer', bearerFormat: 'JWT')]
class OpenApiSpec
{
}
