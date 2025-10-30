<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use Illuminate\Http\Client\Response;
use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasNginxTemplate;

readonly class GetNginxTemplateByName
{
    use HasNginxTemplate;

    /**
     * GetNginxTemplateByName constructor.
     */
    public function __construct(private Client $client) {}

    /**
     * Handle request.
     *
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function handle(int $serverId, string $templateName): ?NginxTemplate
    {
        $response = $this->client->get(
            $this->client->path('/servers/%s/nginx/templates', $serverId),
            ['filter[name]' => $templateName]
        )->throw();

        if ($response->getStatusCode() !== 200) {
            throw new RequestFailed(
                'The nginx template "'.$templateName.'" does not exist.'
            );
        }

        $data = $this->responseData($response);

        if (empty($data)) {
            return null;
        }

        return $this->makeNginxTemplate($serverId, $data);
    }

    /**
     * Get the response data.
     *
     * @return array<string, mixed>
     */
    private function responseData(Response $response): array
    {
        $data = $response->json('data.0');

        return is_array($data) ? $data : [];
    }
}
