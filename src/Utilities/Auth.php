<?php

namespace KyleWLawrence\Cloudflare\Api\Utilities;

use KyleWLawrence\Cloudflare\Api\Exceptions\AuthException;
use Psr\Http\Message\RequestInterface;

/**
 * Class Auth
 * This helper would manage all Authentication related operations.
 */
class Auth
{
    /**
     * The authentication setting to use a Bearer API Token.
     */
    const BEARER = 'bearer';

    /**
     * @var string
     */
    protected $authStrategy;

    /**
     * @var array
     */
    protected $authOptions;

    /**
     * Returns an array containing the valid auth strategies
     *
     * @return array
     */
    protected static function getValidAuthStrategies()
    {
        return [self::BEARER];
    }

    /**
     * Auth constructor.
     *
     * @param    $strategy
     * @param  array  $options
     *
     * @throws AuthException
     */
    public function __construct(string $strategy, array $options)
    {
        if (! in_array($strategy, self::getValidAuthStrategies())) {
            throw new AuthException('Invalid auth strategy set, please use `'
                                    .implode('` or `', self::getValidAuthStrategies())
                                    .'`');
        }

        $this->authStrategy = $strategy;

        if ($strategy == self::BEARER) {
            if (! array_key_exists('token', $options) || ! array_key_exists('email', $options)) {
                throw new AuthException('Please supply `token` and `email` for bearer auth.');
            }
        }

        $this->authOptions = $options;
    }

    /**
     * @param  RequestInterface  $request
     * @param  array  $requestOptions
     * @return array
     *
     * @throws AuthException
     */
    public function prepareRequest(RequestInterface $request, array $requestOptions = []): array
    {
        if ($this->authStrategy === self::BEARER) {
            $token = $this->authOptions['bearer']['token'];
            $email = $this->authOptions['bearer']['email'];
            $request = $request->withAddedHeader('Authorization', " Bearer $bearer")->withAddedHeader('X-Auth-Email', $email);
        } else {
            throw new AuthException('Please set authentication to send requests.');
        }

        return [$request, $requestOptions];
    }
}
