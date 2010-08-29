<?php
namespace Swiftriver\Core\PreProcessing;
/**
 * Class that manages the process of passing all content 
 * through the configured stack of pre processors
 * 
 * @author mg@swiftly.org
 */
class PreProcessor
{
    /**
     * Array of all configured pre processing steps.
     * @var IPreProcessingStep[]
     */
    private $preProcessingSteps;

    /**
     * Constructor for te PreProcessor
     * @var string|null $modulesDirectory
     */
    public function __construct($modulesDirectory = null)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::__construct [Method invoked]", \PEAR_LOG_DEBUG);
        
        $logger->log("Core::PreProcessing::PreProcessor::__construct [START: Adding configured pre processors]", \PEAR_LOG_DEBUG);
        
        $this->preProcessingSteps = \Swiftriver\Core\Setup::PreProcessingStepsConfiguration()->PreProcessingSteps;
        
        $logger->log("Core::PreProcessing::PreProcessor::__construct [END: Adding configured pre processors]", \PEAR_LOG_DEBUG);

        $logger->log("Core::PreProcessing::PreProcessor::__construct [Method finished]", \PEAR_LOG_DEBUG);
    }

    public function PreProcessContent($content)
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Method invoked]", \PEAR_LOG_DEBUG);

        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        $configuration = \Swiftriver\Core\Setup::Configuration();

        if(isset($this->preProcessingSteps) && count($this->preProcessingSteps) > 0)
        {
            foreach($this->preProcessingSteps as $preProcessingStep)
            {
                //Get the class name from config
                $className = $preProcessingStep->className;

                //get the file path from config
                $filePath = $modulesDirectory . $preProcessingStep->filePath;

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Including pre processor: $filePath]", \PEAR_LOG_DEBUG);

                //Include the file
                include_once($filePath);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Including pre processor: $filePath]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Instanciating pre processor: $className]", \PEAR_LOG_DEBUG);

                try
                {
                    //Instanciate the pre processor
                    $preProcessor = new $className();
                }
                catch (\Exception $e)
                {
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [$e]", \PEAR_LOG_ERR);
                    
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Unable to run PreProcessing for preprocessor $className]", \PEAR_LOG_ERR);

                    continue;
                }

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Instanciating pre processor: $className]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [START: Run PreProcessing for $className]", \PEAR_LOG_DEBUG);

                try
                {
                    //Run the preocess method on the pre processor
                    $content = $preProcessor->Process($content, $configuration, $logger);
                }
                catch (\Exception $e)
                {
                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [$e]", \PEAR_LOG_ERR);

                    $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Unable to run PreProcessing for preprocessor $className]", \PEAR_LOG_ERR);
                }

                $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [END: Run PreProcessing for $className]", \PEAR_LOG_DEBUG);
            }
        } 
        else
        {
            $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [No PreProcessing Steps found to run]", \PEAR_LOG_DEBUG);
        }

        $logger->log("Core::PreProcessing::PreProcessor::PreProcessContent [Method finished]", \PEAR_LOG_DEBUG);
        
        //Return the content
        return $content;
    }

    /**
     * Returns all the classes that implment the IPreProcessor interface
     *
     * @return IPreProcessingStep[]
     */
    public function ListAllAvailablePreProcessingSteps()
    {
        $logger = \Swiftriver\Core\Setup::GetLogger();

        $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Method invoked]", \PEAR_LOG_DEBUG);

        $steps = array();

        $modulesDirectory = \Swiftriver\Core\Setup::Configuration()->ModulesDirectory;

        $dirItterator = new \RecursiveDirectoryIterator($modulesDirectory);

        $iterator = new \RecursiveIteratorIterator($dirItterator, \RecursiveIteratorIterator::SELF_FIRST);
        
        foreach($iterator as $file)
        {
            if(!$file->isFile())
                continue;
            
            $filePath = $file->getPathname();

            if(!strpos($filePath, "PreProcessingStep.php"))
                continue;

            try
            {
                include_once($filePath);

                $typeString = "\\Swiftriver\\PreProcessingSteps\\".$file->getFilename();

                $type = str_replace(".php", "", $typeString);

                $object = new $type();
                
                if($object instanceof IPreProcessingStep)
                {
                    $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Adding type $type]", \PEAR_LOG_DEBUG);

                    $object->filePath = str_replace($modulesDirectory, "", $filePath);

                    $object->type = $type;

                    $steps[] = $object;
                }
            }
            catch(\Exception $e)
            {
                $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [error adding type $type]", \PEAR_LOG_DEBUG);

                $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [$e]", \PEAR_LOG_ERR);

                continue;
            }
        }

        $logger->log("Core::PreProcessing::PreProcessor::ListAllAvailablePreProcessingSteps [Method finished]", \PEAR_LOG_DEBUG);

        return $steps;
    }
}
?>