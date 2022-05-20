<?php
declare(strict_types=1);

namespace App\OpenApi;

use ApiPlatform\Core\OpenApi\Factory\OpenApiFactoryInterface;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\Core\OpenApi\Model;

final class JwtDecorator implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(
        OpenApiFactoryInterface $decorated
    )
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = ($this->decorated)($context);
        $schemas = $openApi->getComponents()->getSchemas();

        $schemas['Token'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true
                ],
                'refresh_token_expiration' => [
                    'type' => 'number',
                    'readOnly' => true
                ]
            ],
        ]);

        $schemas['Refresh'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
                'refresh_token_expiration' => [
                    'type' => 'string',
                    'readOnly' => true,
                ],
            ],
        ]);

        $schemas['Credentials'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'username' => [
                    'type' => 'string',
                    'example' => 'username',
                ],
                'password' => [
                    'type' => 'string',
                    'example' => 'password',
                ],
            ],
        ]);

        $schemas['TokenToRefresh'] = new \ArrayObject([
            'type' => 'object',
            'properties' => [
                'refresh_token' => [
                    'type' => 'string',
                    'example' => 'sjfg0ks.djGKsdjfgo-',
                ],
            ],
        ]);

        $pathItem = new Model\PathItem(
            'Authentication JWT Token',
            null,
            null,
            null,
            null,
            new Model\Operation(
                'postCredentialsItem',
                ['Authentication'],
                [
                    '200' => [
                        'description' => 'Get JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Token',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get JWT token to login.',
                '',
                null,
                [],
                new Model\RequestBody(
                    'Generate new JWT Token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/Credentials',
                            ],
                        ],
                    ])
                )
            )
        );
        $openApi->getPaths()->addPath('/login', $pathItem);

        $pathItem = new Model\PathItem(
            'Refresh JWT',
            null,
            null,
            null,
            null,
            new Model\Operation(
                'postTokenToRefresh',
                ['Authentication'],
                [
                    '200' => [
                        'description' => 'Get new JWT token',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    '$ref' => '#/components/schemas/Refresh',
                                ],
                            ],
                        ],
                    ],
                ],
                'Get new JWT token to login.',
                '',
                null,
                [],
                new Model\RequestBody(
                    'Generate new JWT Token',
                    new \ArrayObject([
                        'application/json' => [
                            'schema' => [
                                '$ref' => '#/components/schemas/TokenToRefresh',
                            ],
                        ],
                    ])
                )
            )
        );
        $openApi->getPaths()->addPath('/refresh', $pathItem);

        return $openApi;
    }
}

