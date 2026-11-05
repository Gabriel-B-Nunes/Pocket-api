<?php

declare(strict_types=1);

namespace App\service\documentation;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: 'Pocket API',
    version: '1.0.0',
    description: 'Backend REST API for the Pocket application. Authenticated CRUD for users, groups, items, movements, movement items, installments and transactions.',
    contact: new OA\Contact(name: 'Pocket API')
)]
#[OA\Server(url: 'http://localhost:8080', description: 'Local development')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'JWT access token issued by /api/v1/users/login'
)]
#[OA\Tag(name: 'Users', description: 'Registration, login and token refresh')]
#[OA\Tag(name: 'Groups', description: 'User-owned collections')]
#[OA\Tag(name: 'Items', description: 'Products/materials linked to a group')]
#[OA\Tag(name: 'Movements', description: 'Stock/inventory movements')]
#[OA\Tag(name: 'MovementItems', description: 'Items within a movement')]
#[OA\Tag(name: 'Installments', description: 'Payment installments of a movement')]
#[OA\Tag(name: 'Transactions', description: 'Payments against an installment')]
class OpenApiSpec
{
}
