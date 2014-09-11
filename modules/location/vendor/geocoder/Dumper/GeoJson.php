<?php

/**
 * This file is part of the Geocoder package.
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @license    MIT License
 */

/**
 * @author Jan Sorgalla <jsorgalla@googlemail.com>
 */
class Dumper_GeoJson implements Dumper_Interface
{
    /**
     * @param  \Geocoder\Result\ResultInterface $result
     * @return string
     */
    public function dump(Result_Interface $result)
    {
        $properties = array_filter($result->toArray(), function($val) {
            return $val !== null;
        });

        unset($properties['latitude'], $properties['longitude'], $properties['bounds']);

        if (count($properties) === 0) {
            $properties = null;
        }

        $json = array(
            'type' => 'Feature',
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array($result->getLongitude(), $result->getLatitude())
            ),
            'properties' => $properties
        );

        // Custom bounds property
        $bounds = $result->getBounds();

        if (null !== $bounds) {
            $json['bounds'] = $bounds;
        }

        return json_encode($json);
    }
}
