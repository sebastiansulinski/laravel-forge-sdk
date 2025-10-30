<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class CreateSitePayload implements Arrayable
{
    /**
     * CreateSitePayload constructor.
     */
    public function __construct(
        public string $type,
        public string $domain_mode,
        public string $name,
        public string $web_directory,
        public string $php_version,
        public string $source_control_provider,
        public string $repository,
        public string $branch,
        public string $www_redirect_type = 'none',
        public bool $allow_wildcard_subdomains = false,
        public bool $install_composer_dependencies = true,
        public bool $generate_deploy_key = false,
        public bool $push_to_deploy = false,
        public bool $zero_downtime_deployments = false,
        public ?int $nginx_template_id = null
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type,
            'domain_mode' => $this->domain_mode,
            'name' => $this->name,
            'web_directory' => $this->web_directory,
            'php_version' => $this->php_version,
            'source_control_provider' => $this->source_control_provider,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'www_redirect_type' => $this->www_redirect_type,
            'allow_wildcard_subdomains' => $this->allow_wildcard_subdomains,
            'install_composer_dependencies' => $this->install_composer_dependencies,
            'generate_deploy_key' => $this->generate_deploy_key,
            'push_to_deploy' => $this->push_to_deploy,
            'zero_downtime_deployments' => $this->zero_downtime_deployments,
            'nginx_template_id' => $this->nginx_template_id,
        ], fn ($value) => $value !== null);
    }
}
