<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Class to hold Global Positioning data associated
 * with a content object
 *
 * @author mg@swiftly.org
 */
class GisData
{
    /**
     * The longitude
     *
     * @var float
     */
    public $longitude;

    /**
     * The latitude
     *
     * @var float
     */
    public $latitude;

    /**
     * Constructor for the GisData object
     * 
     * @param float $longitude
     * @param float $latitude
     */
    public function __construct($longitude, $latitude)
    {
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }
}
?>
