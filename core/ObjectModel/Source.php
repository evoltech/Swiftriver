<?php
namespace Swiftriver\Core\ObjectModel;
/**
 * Source object
 * @author mg@swiftly.org
 */
class Source
{
    /**
     * The genuine unique ID of this source
     * @var string
     */
    public $id;

    /**
     * The trust score for this source
     * @var int
     */
    public $score;

    /**
     * The friendly name of this source
     * @var string
     */
    public $name;

    /**
     * The email address of the source
     * @var string
     */
    public $email;

    /**
     * The link to the source
     * @var string
     */
    public $link;

    /**
     * The ID of the parent channel object
     * @var string
     */
    public $parent;

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
}
?>
