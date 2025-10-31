<?php

namespace SebastianSulinski\LaravelForgeSdk\Traits;

use Carbon\Carbon;
use SebastianSulinski\LaravelForgeSdk\Data\Certificate;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateKeyType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateRequestStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateStatus;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateType;
use SebastianSulinski\LaravelForgeSdk\Enums\CertificateVerificationMethod;

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
    protected function makeCertificate(int $serverId, int $siteId, int $domainRecordId, array $data): Certificate
    {
        $attributes = $data['attributes'];

        return new Certificate(
            id: $data['id'],
            serverId: $serverId,
            siteId: $siteId,
            domainRecordId: $domainRecordId,
            type: CertificateType::from($attributes['type']),
            requestStatus: CertificateRequestStatus::from($attributes['request_status']),
            status: CertificateStatus::from($attributes['status']),
            verificationMethod: isset($attributes['verification_method'])
                ? CertificateVerificationMethod::from($attributes['verification_method'])
                : null,
            keyType: isset($attributes['key_type'])
                ? CertificateKeyType::from($attributes['key_type'])
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
