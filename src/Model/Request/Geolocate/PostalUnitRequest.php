<?php

declare(strict_types=1);

namespace Velhron\DadataBundle\Model\Request\Geolocate;

class PostalUnitRequest extends GeolocateRequest
{
    /**
     * {@inheritdoc}
     */
    protected function getMethodUrl(): string
    {
        return 'postal_unit';
    }
}
