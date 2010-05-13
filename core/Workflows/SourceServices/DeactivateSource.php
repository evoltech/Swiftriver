<?php
namespace Swiftriver\Core\Workflows\SourceServices;
class DeactivateSource extends SourceServicesBase {
    /**
     * Activates a source based on the source ID
     * encode in the JSON param
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($json, $key) {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        //try to parse the id from the JSON
        try {
            //get the ID from the JSON
            $id = parent::ParseJSONToId($json);
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [START: Getting the source from the repository]", \PEAR_LOG_DEBUG);

        try {
            //Get the channel from the repo
            $source = $repository->GetSourceById($id);
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [END: Getting the source from the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [START: Marking source as inactive and saving to the repository]", \PEAR_LOG_DEBUG);

        try {
            //set the active flag to true
            $source->active = false;

            //save the channel back to the repo
            $repository->SaveSource($source);
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [END: Marking source as inactive and saving to the repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::DeactivateSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return an OK messagae
        return parent::FormatMessage("OK");
    }
}
?>