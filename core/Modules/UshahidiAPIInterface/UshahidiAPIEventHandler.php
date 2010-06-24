<?php
namespace Swiftriver\EventHandlers;
class UshahidiAPIEventHandler implements \Swiftriver\Core\EventDistribution\IEventHandler {
    /**
     * This method should return the name of the event handler
     * that you implement. This name should be unique across all
     * event handlers and should be no more that 50 chars long
     *
     * @return string
     */
    public function Name() {
        return "Ushahidi Report Push";
    }

    /**
     * This method should return a description describing what
     * exactly it is that your Event Handler does
     *
     * @return string
     */
    public function Description() {
        return "Activating this Reactor Turbine will cause all content that you " .
               "mark as accurate to be sent to the associated Ushahidi instance " .
               "as a new report.";
    }

    /**
     * This method returns an array of the required paramters that
     * are nessesary to configure this event handler.
     *
     * @return \Swiftriver\Core\ObjectModel\ConfigurationElement[]
     */
    public function ReturnRequiredParameters(){
        return array(
            new \Swiftriver\Core\ObjectModel\ConfigurationElement(
                    "UshahidiUrl",
                    "string",
                    "The fully qualified url to the Ushahidi instance you want to " .
                    "communicate with (please dont include the API path, just the root)")
        );
    }

    /**
     * This method should return the names of the events
     * that your EventHandler wishes to subscribe to.
     *
     * @return string[]
     */
    public function ReturnEventNamesToHandle() {
        return array(
            \Swiftriver\Core\EventDistribution\EventEnumeration::$MarkContentAsAccurate,
        );
    }

    /**
     * Given a GenericEvent object, this method should do
     * something amazing with the data contained in the
     * event arguments.
     *
     * @param GenericEvent $event
     * @param \Swiftriver\Core\Configuration\ConfigurationHandlers\CoreConfigurationHandler $configuration
     * @param \Log $logger
     */
    public function HandleEvent($event, $configuration, $logger) {
        $logger->log("Swiftriver::EventHandlers::UshahidiAPIEventHandler::HandleEvent [Method invoked]", \PEAR_LOG_DEBUG);

        //Get the $event->arguments as a content item
        $content = $event->arguments;

        //check that arguments property of the $event passed in is a content item
        if(!\Swiftriver\Core\ObjectModel\TypeComparisons\IsContent::CheckType($content)) {
            $logger->log("Swiftriver::EventHandlers::UshahidiAPIEventHandler::HandleEvent [The obejct passed in the GenericEvent->arguments property was not of type Content.]", \PEAR_LOG_DEBUG);
            $logger->log("Swiftriver::EventHandlers::UshahidiAPIEventHandler::HandleEvent [Method finished]", \PEAR_LOG_DEBUG);
            return;
        }

        //Instanciate the parser that will be used to parse the content item into Ushahidi format
        $toUshahidiParser = new \Swiftriver\Core\EventHandlers\UshahidiAPIInterface\ContentToUshahidiAPIParser();

        //Get the ushahidi formatted params from the parser
        $query = $toUshahidiParser->ParseContentItemToUshahidiAPIFormat($content);



        $logger->log("Swiftriver::EventHandlers::UshahidiAPIEventHandler::HandleEvent [Method finished]", \PEAR_LOG_DEBUG);
    }
}
?>
