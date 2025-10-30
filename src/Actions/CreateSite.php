<?php

namespace SebastianSulinski\LaravelForgeSdk\Actions;

use SebastianSulinski\LaravelForgeSdk\Client;
use SebastianSulinski\LaravelForgeSdk\Data\Site;
use SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed;
use SebastianSulinski\LaravelForgeSdk\Payload\CreateSitePayload;
use SebastianSulinski\LaravelForgeSdk\Traits\HasSite;
use Illuminate\Container\Attributes\Config;

readonly class CreateSite
{
    use HasSite;

    /**
     * CreateSite constructor.
     */
    public function __construct(
        private Client $client,
        private GetNginxTemplateByName $getNginxTemplate,
        #[Config('services.forge.nginx_template_name')] private string $nginxTemplateName,
    ) {}

    /**
     * Handle request.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     * @throws \Exception
     */
    public function handle(int $serverId, CreateSitePayload $payload): Site
    {
        $path = $this->client->path(sprintf('/servers/%s/sites', $serverId));

        $this->setDefaultNginxTemplateId(
            serverId: $serverId,
            payload: $payload
        );

        $response = $this->client->post($path, $payload->toArray())->throw();

        if (! $response->successful()) {
            throw new RequestFailed(
                $response->json('message', 'Response returned: '.$response->status())
            );
        }

        return $this->makeSite($serverId, $response->json('data'));
    }

    /**
     * Set default nginx template ID.
     *
     * @throws \SebastianSulinski\LaravelForgeSdk\Exceptions\RequestFailed
     * @throws \Illuminate\Http\Client\ConnectionException
     * @throws \Illuminate\Http\Client\RequestException
     */
    private function setDefaultNginxTemplateId(int $serverId, CreateSitePayload $payload): void
    {
        if (! $payload->nginx_template_id) {
            $nginxTemplate = $this->getNginxTemplate->handle(
                $serverId, $this->nginxTemplateName
            );

            if (! $nginxTemplate) {
                throw new RequestFailed(
                    'The nginx template "'.$this->nginxTemplateName.'" does not exist.'
                );
            }

            $payload->nginx_template_id = $nginxTemplate->id;
        }
    }
}
