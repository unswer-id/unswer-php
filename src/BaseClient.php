<?php

namespace Unswer;

use Unswer\Exceptions\UnswerException;
use Rakit\Validation\Validator;
use Unswer\Rules\CursorRule;
use Unswer\Rules\SnakeCaseRule;

class BaseClient
{
    protected static ?string $apiKey;
    protected static ?string $appId;
    protected static Http $http;
    protected static Validator $validator;

    private function validator()
    {
        $validator = new Validator();
        $validator->addValidator('cursor', new CursorRule());
        $validator->addValidator('snake_case', new SnakeCaseRule());

        return $validator;
    }

    public function __construct(?string $apiKey = null, ?string $appId = null, array $config = [])
    {
        $apiKey = self::$apiKey ?: ($apiKey ?: getenv('UNSWER_API_KEY'));
        $appId = self::$appId ?: ($appId ?: getenv('UNSWER_APP_ID'));

        if (empty($apiKey) || empty($appId)) {
            throw new UnswerException('API Key and App ID must be configured');
        }

        self::$apiKey = $apiKey;
        self::$appId = $appId;
        self::$http ??= new Http($apiKey, $config);
        self::$validator ??= $this->validator();
    }
}
