<?php

namespace SebastianSulinski\LaravelForgeSdk\Data\Concerns;

/**
 * Provides access to both relationships and links metadata.
 * Use this trait when your data object has both properties.
 * For objects with only one, use HasRelationships or HasLinks directly.
 */
trait HasApiMetadata
{
    use HasLinks;
    use HasRelationships;
}
