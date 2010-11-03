<?php
namespace Swiftriver\Core\Analytics;
/**
 * @author mg[at]swiftly[dot]org
 */
class AnalyticsRequest
{
    /**
     * The name of the Analytics Provider that
     * should be used to fulfill this analytics
     * request.
     *
     * @var string
     */
    public $RequestType;

    /**
     * Parameters that are passed to the Analytics
     * Provider in order for it to fulfill the
     * request.
     *
     * @var object
     */
    public $Parameters;

    /**
     * The result of the Analytics request.
     * 
     * @var object
     */
    public $Result;
}
?>
