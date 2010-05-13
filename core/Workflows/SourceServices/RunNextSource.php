<?php
namespace Swiftriver\Core\Workflows\SourceServices;
class RunNextSource extends SourceServicesBase {
    /**
     * Selects the next due processing job and runs it through the core
     *
     * @return string $json
     */
    public function RunWorkflow($key) {
        //Setup the logger
        $logger = \Swiftriver\Core\Setup::GetLogger();
        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method invoked]", \PEAR_LOG_INFO);

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Setting time out]", \PEAR_LOG_DEBUG);
        
        set_time_limit(300);
        
        $timeout = ini_get('max_execution_time');

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Setting time out to $timeout]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Constructing Repository]", \PEAR_LOG_DEBUG);

        try {
            //Construct a new repository
            $sourceRepository = new \Swiftriver\Core\DAL\Repositories\SourceRepository();
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Constructing Repository]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Fetching next source]", \PEAR_LOG_DEBUG);

        try {
            //Get the next due channel processign job
            $source = $sourceRepository->SelectNextDueSource(time());
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }


        if($source == null) {
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [INFO: No source due]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Fetching next source]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatMessage("OK");
        }

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Fetching next source]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Get and parse content]", \PEAR_LOG_DEBUG);

        try {
            $SiSPS = new \Swiftriver\Core\Modules\SiSPS\SwiftriverSourceParsingService();
            $rawContent = $SiSPS->FetchContentFromChannel($source);
        }
        catch (\Exception $e) {
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);

            try {
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Mark source as in error]", \PEAR_LOG_DEBUG);

                $source->inprocess = false;
                $sourceRepository->SaveSources(array($source));
                
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Mark source as in error]", \PEAR_LOG_DEBUG);
            }
            catch(\Exception $innerE) {
                $message = $innerE->getMessage();
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [This source will remain in state - in progress - and will not be run again, manual action must be taken.]", \PEAR_LOG_ERR);
            }

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

            return parent::FormatErrorMessage("An exception was thrown: $message");
        }


        if(isset($rawContent) && is_array($rawContent) && count($rawContent) > 0) {

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Get and parse content]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Running core processing]", \PEAR_LOG_DEBUG);

            try {
                $preProcessor = new \Swiftriver\Core\PreProcessing\PreProcessor();
                $processedContent = $preProcessor->PreProcessContent($rawContent);
            }
            catch (\Exception $e) {
                //get the exception message
                $message = $e->getMessage();
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
                return parent::FormatErrorMessage("An exception was thrown: $message");
            }

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Running core processing]", \PEAR_LOG_DEBUG);

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Save content to the data store]", \PEAR_LOG_DEBUG);

            try {
                $contentRepository = new \Swiftriver\Core\DAL\Repositories\ContentRepository();
                $contentRepository->SaveContent($processedContent);
            }
            catch (\Exception $e) {
                //get the exception message
                $message = $e->getMessage();
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
                $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
                return parent::FormatErrorMessage("An exception was thrown: $message");
            }

            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Save content to the data store]", \PEAR_LOG_DEBUG);
        }
        else {
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Get and parse content]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [No content found.]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [START: Mark channel processing job as complete]", \PEAR_LOG_DEBUG);

        try {
            $source->inprocess = false;
            $source->lastSucess = time();
            $sourceRepository->SaveSources(array($source));
        }
        catch (\Exception $e) {
            //get the exception message
            $message = $e->getMessage();
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [An exception was thrown]", \PEAR_LOG_DEBUG);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [$message]", \PEAR_LOG_ERR);
            $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);
            return parent::FormatErrorMessage("An exception was thrown: $message");
        }

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [END: Mark channel processing job as complete]", \PEAR_LOG_DEBUG);

        $logger->log("Core::Workflows::SourceServices::RunNextSource::RunWorkflow [Method finished]", \PEAR_LOG_INFO);

        return parent::FormatMessage("OK");
    }
}
?>
