<?php
/**
 * @author Jacco.Amersfoort <jacco.amersfoort@monta.nl>
 * @created 11/5/2025 15:53
 */
namespace Monta\Checkout\Block;

use Magento\PageBuilder\Block\GoogleMapsApi;

class GoogleMaps extends GoogleMapsApi
{
    /** Get URL for Google Maps library script
     *
     * @return string
     */
    public function getLibraryUrl(): string
    {
        return
            // trim parameters from constant
            strtok(string: self::GOOGLE_MAPS_LIBRARY_URL, token: '?')
            // Append parameters properly from array
            . "?" . http_build_query([
                // get recent, stable version
                'v' => 'quarterly',
                'key' => $this->getApiKey(),
            ]);
    }

}