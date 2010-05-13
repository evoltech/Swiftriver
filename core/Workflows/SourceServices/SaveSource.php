<?php
namespace Swiftriver\Core\Workflows\SourceServices;
class SaveSource extends SourceServicesBase {
    /**
     * Adds the pre processing job to the DAL
     *
     * @param string $json
     * @return string $json
     */
    public function RunWorkflow($json, $key) {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [START: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        try {
            //Parse the JSON input
            $source = parent::ParseJSONToSource($json);
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [END: Parsing the JSON input]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try {
            //Construct a new repository
            $repository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [START: Saving source]", \PEAR_LOG_DEBUG);

        try {
            //Add the source to the repository
            $repository->SaveSources(array($source));
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [END: Saving source]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::SaveSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        //return an OK messagae
        return parent::FormatMessage("OK");
    }
}
?>
