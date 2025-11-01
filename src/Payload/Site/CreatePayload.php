<?php

namespace SebastianSulinski\LaravelForgeSdk\Payload\Site;

use Illuminate\Contracts\Support\Arrayable;
use SebastianSulinski\LaravelForgeSdk\Enums\Repository\Provider;
use SebastianSulinski\LaravelForgeSdk\Enums\Server\PhpVersion;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\DomainMode;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Site\WwwRedirectType;

/**
 * @implements Arrayable<string, mixed>
 */
readonly class CreatePayload implements Arrayable
{
    /**
     * CreatePayload constructor.
     *
     * @param  array<int, string>|null  $tags
     * @param  array<int, string>|null  $shared_paths
     */
    public function __construct(
        public Type $type,
        public ?DomainMode $domain_mode = null,
        public ?string $name = null,
        public ?WwwRedirectType $www_redirect_type = null,
        public ?string $allow_wildcard_subdomains = null,
        public ?string $web_directory = null,
        public ?bool $is_isolated = null,
        public ?string $isolated_user = null,
        public ?PhpVersion $php_version = null,
        public ?bool $zero_downtime_deployments = null,
        public ?int $nginx_template_id = null,
        public ?Provider $source_control_provider = null,
        public ?string $repository = null,
        public ?string $branch = null,
        public ?int $database_id = null,
        public ?string $database_user_id = null,
        public ?string $statamic_setup = null,
        public ?string $statamic_starter_kit = null,
        public ?string $statamic_super_user_email = null,
        public ?string $statamic_super_user_password = null,
        public ?bool $install_composer_dependencies = null,
        public ?bool $generate_deploy_key = null,
        public ?string $public_deploy_key = null,
        public ?string $private_deploy_key = null,
        public ?string $nuxt_next_mode = null,
        public ?string $nuxt_next_build_command = null,
        public ?int $nuxt_next_port = null,
        public ?bool $push_to_deploy = null,
        public ?array $tags = null,
        public ?array $shared_paths = null
    ) {}

    /**
     * {@inheritDoc}
     */
    public function toArray(): array
    {
        return array_filter([
            'type' => $this->type->value,
            'domain_mode' => $this->domain_mode?->value,
            'name' => $this->name,
            'www_redirect_type' => $this->www_redirect_type?->value,
            'allow_wildcard_subdomains' => $this->allow_wildcard_subdomains,
            'web_directory' => $this->web_directory,
            'is_isolated' => $this->is_isolated,
            'isolated_user' => $this->isolated_user,
            'php_version' => $this->php_version?->value,
            'zero_downtime_deployments' => $this->zero_downtime_deployments,
            'nginx_template_id' => $this->nginx_template_id,
            'source_control_provider' => $this->source_control_provider?->value,
            'repository' => $this->repository,
            'branch' => $this->branch,
            'database_id' => $this->database_id,
            'database_user_id' => $this->database_user_id,
            'statamic_setup' => $this->statamic_setup,
            'statamic_starter_kit' => $this->statamic_starter_kit,
            'statamic_super_user_email' => $this->statamic_super_user_email,
            'statamic_super_user_password' => $this->statamic_super_user_password,
            'install_composer_dependencies' => $this->install_composer_dependencies,
            'generate_deploy_key' => $this->generate_deploy_key,
            'public_deploy_key' => $this->public_deploy_key,
            'private_deploy_key' => $this->private_deploy_key,
            'nuxt_next_mode' => $this->nuxt_next_mode,
            'nuxt_next_build_command' => $this->nuxt_next_build_command,
            'nuxt_next_port' => $this->nuxt_next_port,
            'push_to_deploy' => $this->push_to_deploy,
            'tags' => $this->tags,
            'shared_paths' => $this->shared_paths,
        ], fn ($value) => $value !== null);
    }
}
