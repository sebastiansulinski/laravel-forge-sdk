<?php

namespace SebastianSulinski\LaravelForgeSdk;

use Illuminate\Container\Attributes\Config;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use JsonSerializable;

readonly class Client
{
    private const string BASE_URL = 'https://forge.laravel.com/api';

    /**
     * Client constructor.
     */
    public function __construct(
        #[Config('forge.token')]
        private string $token,
        #[Config('forge.timeout')]
        private int $timeout,
        #[Config('forge.organisation')]
        public string $organisation,
    ) {}

    /**
     * Perform GET request.
     *
     * @param  string|array<string, mixed>|null  $query
     * @param  array<string, string>|null  $headers
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function get(
        string $path,
        string|array|null $query = null,
        ?array $headers = null
    ): Response {
        return $this->http($headers)->get($path, $query);
    }

    /**
     * Get an instance of the http client.
     *
     * @param  array<string, string>|null  $headers
     */
    public function http(?array $headers = null): PendingRequest
    {
        $request = Http::baseUrl(self::BASE_URL)
            ->asJson()
            ->acceptJson()
            ->withToken($this->token)
            ->timeout($this->timeout);

        if ($headers) {
            return $request->withHeaders($headers);
        }

        return $request;
    }

    /**
     * Perform POST request.
     *
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable  $data
     * @param  array<string, string>|null  $headers
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function post(
        string $path,
        array|Arrayable|JsonSerializable $data = [],
        ?array $headers = null
    ): Response {
        return $this->http($headers)->post($path, $data);
    }

    /**
     * Perform PATCH request.
     *
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable  $data
     * @param  array<string, string>|null  $headers
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function patch(
        string $path,
        array|Arrayable|JsonSerializable $data = [],
        ?array $headers = null
    ): Response {
        return $this->http($headers)->patch($path, $data);
    }

    /**
     * Perform PUT request.
     *
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable  $data
     * @param  array<string, string>|null  $headers
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function put(
        string $path,
        array|Arrayable|JsonSerializable $data = [],
        ?array $headers = null
    ): Response {
        return $this->http($headers)->put($path, $data);
    }

    /**
     * Perform DELETE request.
     *
     * @param  array<string, mixed>|Arrayable<string, mixed>|JsonSerializable  $data
     * @param  array<string, string>|null  $headers
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     */
    public function delete(
        string $path,
        array|Arrayable|JsonSerializable $data = [],
        ?array $headers = null
    ): Response {
        return $this->http($headers)->delete($path, $data);
    }

    /**
     * Parse path by prepending organisation.
     */
    public function path(string $path, string|int ...$arguments): string
    {
        $basePath = '/orgs/%s/'.ltrim($path, '/');

        return vsprintf($basePath, [
            $this->organisation,
            ...$arguments,
        ]);
    }
}
