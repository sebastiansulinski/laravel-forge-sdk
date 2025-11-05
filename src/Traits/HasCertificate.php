<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\KeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\RequestStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Status;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\Type;
use SebastianSulinski\LaravelForgeSdk\Enums\Certificate\VerificationMethod;

/**
 * @phpstan-type CertificateData array{
 *     id: int,
 *     attributes: array{
 *         type: string,
 *         request_status: string,
 *         status: string,
 *         verification_method?: string|null,
 *         key_type?: string|null,
 *         preferred_chain?: string|null,
 *         created_at?: string|null,
 *         updated_at?: string|null
 *     }
 * }
 */
trait HasCertificate
{
    /**
     * Make a certificate.
     *
     * @param  CertificateData  $data
     */
    protected function makeCertificate(array $data): Certificate
    {
        $attributes = $data['attributes'];

        return new Certificate(
            id: $data['id'],
            type: Type::from($attributes['type']),
            requestStatus: RequestStatus::from($attributes['request_status']),
            status: Status::from($attributes['status']),
            verificationMethod: isset($attributes['verification_method'])
                ? VerificationMethod::from($attributes['verification_method'])
                : null,
            keyType: isset($attributes['key_type'])
                ? KeyType::from($attributes['key_type'])
                : null,
            preferredChain: $attributes['preferred_chain'] ?? null,
            createdAt: isset($attributes['created_at'])
                ? Carbon::parse($attributes['created_at'])
                : null,
            updatedAt: isset($attributes['updated_at'])
                ? Carbon::parse($attributes['updated_at'])
                : null,
        );
    }
}
