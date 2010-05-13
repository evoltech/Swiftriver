<?php
namespace Swiftriver\Core\ObjectModel;
class Source {
    /**
     * The genuine unique ID of this source
     * 
     * @var string
     */
    public $id;

    /**
     * The trust score for this source
     *
     * @var int
     */
    public $score;

    /**
     * The friendly name of this source
     *
     * @var string
     */
    public $name;

    /**
     * The type of the source - given by the parser
     *
     * @var string
     */
    public $type;

    /**
     * The subtype of the source - given by the parser
     *
     * @var string
     */
    public $subType;

    /**
     * Parameters required to get content
     * For example, parameters may be:
     *  array (
     *      "type" -> "email",
     *      "connectionString" -> "someConnectionString"
     *  );
     *
     * @var string[]
     */
    public $parameters = array();

    /**
     * The period in minutes that the source should be updated
     *
     * @return int
     */
    public $updatePeriod;

    /**
     * The time this source is next due to be run throught
     * the SiSPS
     *
     * @var time
     */
    public $nextrun;

    /**
     * The last time the source was run throught
     * the SiSPS - Note this time is not the last
     * sucess just the last run
     *
     * @var time
     */
    public $lastrun;

    /**
     * The last time this source was sucessfully run
     * though the SiSPS
     *
     * @var time
     */
    public $lastSucess;

    /**
     * A boolean indicating if the source is
     * currently being processed
     *
     * @var bool
     */
    public $inprocess;

    /**
     * the number of sucessful time this source
     * have been run throught the SiSPS
     *
     * @var int
     */
    public $timesrun = 0;

    /**
     * If this job is currently active or not
     * @var bool
     */
    public $active = true;
}
?>
