<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\NginxTemplate;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Traits\HasNginxTemplate;
use SebastianSulinski\LaravelForgeSdk\Traits\ParsesResponse;

/**
 * @phpstan-import-type NginxTemplateData from HasNginxTemplate
 */
readonly class GetNginxTemplateByName
{
    use HasNginxTemplate, ParsesResponse;

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

        $allData = $this->parseDataList($response);

        if (empty($allData)) {
            return null;
        }

        /** @var NginxTemplateData $data */
        $data = $allData[0];

        return $this->makeNginxTemplate($data);
    }
}
