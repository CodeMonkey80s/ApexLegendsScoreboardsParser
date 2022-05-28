<?php

namespace App\Command;

use App\Component\JSON;
use App\Formatter\OutputFormatterStyles;
use Google\ApiCore\ApiException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use Google\Cloud\Vision\V1\Feature\Type;
use Google\Cloud\Vision\V1\ImageAnnotatorClient;
use Google\Cloud\Vision\V1\Likelihood;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpKernel\KernelInterface;

class ScoreboardsProcess extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:scoreboards-process';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Process scoreboards images in selected directory.';

    /**
     * @var string
     */
    protected string $dataFile = '';

    /**
     * @var string
     */
    protected string $keyFile = '';

    /**
     * @var string
     */
    protected string $username = '';

    /**
     * @var string
     */
    protected string $directory = '';

    /**
     * @var bool $optionDebug
     */
    protected bool $optionDebug = false;

    /**
     * @var array
     */
    protected array $area = [];

    /**
     * @param string $keyFile
     * @param string $dataFile
     * @param string $username
     * @param string $directory
     */
    public function __construct(
        string $keyFile,
        string $dataFile,
        string $username,
        string $directory,
        array $area
    ) {
        $this->keyFile = $keyFile;
        $this->dataFile = $dataFile;
        $this->username = $username;
        $this->directory = $directory;
        $this->area = $area;

        parent::__construct();
    }

    /**
     * @param string $path
     * @return string
     * @throws ApiException
     */
    private function parseImage(
        string $path,
        bool $useCropping = false,
    ): string {
        if ($useCropping) {
            $orginainalImage = imagecreatefrompng($path);
            $croppedImage = imagecrop(
                $orginainalImage,
                [
                    'x'      => $this->area[0],
                    'y'      => $this->area[1],
                    'width'  => $this->area[2],
                    'height' => $this->area[3],
                ]
            );
            $temporaryImageFile = $this->directory . '___temporary.png';
            imagepng($croppedImage, $temporaryImageFile);
        } else {
            $temporaryImageFile = $path;
        }
        $imageAnnotator = new ImageAnnotatorClient(
            ['model' => 'buildin/latest']
        );
        $image = file_get_contents($temporaryImageFile);
        $fields = null;
        if ($image !== false) {
            $response = $imageAnnotator->textDetection($image);
            $fields = $response->getTextAnnotations();
    	}
        unlink($temporaryImageFile);
        $imageAnnotator->close();
        if ($fields && $fields->count() > 0) {
            if ($this->optionDebug) {
                echo "\n***** OCR OUTPUT *****\n";
                print_r($fields->offsetGet(0)->getDescription());
                echo "=========\n";
            }
            return $fields->offsetGet(0)->getDescription();
        } else {
            return '';
        }
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setHelp('This command parses all images in selected directory and sends them to Google Vision OCR for processing.')
            ->addOption('debug', null, InputOption::VALUE_NONE, 'Prints debug messages.');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function initialize(
        InputInterface $input,
        OutputInterface $output
    ): void {
        $this->optionDebug = $input->getOption('debug');
        if (empty($this->username)) {
            throw new InvalidArgumentException("*** ERROR *** Username: '$this->username' is empty!");
        }
        if (!file_exists($this->directory)) {
            throw new InvalidArgumentException("*** ERROR *** Directory: '$this->directory' does not exists!");
        }
        if (!file_exists($this->keyFile)) {
            throw new InvalidArgumentException("*** ERROR *** Key File: '$this->keyFile' does not exists!");
        }
        $formatter = new OutputFormatter(
            true,
            OutputFormatterStyles::getStyles()
        );
        $output->setFormatter($formatter);
        putenv("GOOGLE_APPLICATION_CREDENTIALS=$this->keyFile");
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ApiException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $output->write("<head>Parsing Scoreboards Images...</head>\n\n");
        $output->write("<info>Directory   :  \"$this->directory\"</info>\n");

        $json = new JSON();
        $data = $json->loadJSONFile($this->dataFile);

        $filenames = [];
        foreach (new \DirectoryIterator($this->directory) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }
            $filename = $fileInfo->getFilename();
            if (str_ends_with($filename, 'png')) {
                $path = $fileInfo->getRealPath();
                $filenames[$filename] = $path;
            }
        }
        natsort($filenames);

        $totalImages = count($filenames);
        $output->write("<info>Images      :  $totalImages</info>\n\n");
        if ($totalImages == 0) {
            $output->write("<fail>*** ERROR *** Directory is empty!</fail>\n\n");

            return Command::FAILURE;
        }

        $image = 0;
        $skippedImages = 0;
        $processedImages = 0;
        foreach ($filenames as $filename => $path) {
            $image++;
            $hash = md5($filename);

            $fileinfo = pathinfo($filename, PATHINFO_FILENAME);
            $date = explode('_', $fileinfo)[1];
            $datetime = \DateTime::createFromFormat('Y.m.d-H.i', $date);
            $fulldate =  $datetime->format("Y/m/d H:i");
            if (!$fulldate) {
                $fulldate == 'Unknown';
            }
            $output->write("<info>ID          :  $image</info>\n");
            $output->write("<info>Hash        :  $hash</info>\n");
            $output->write("<info>Filename    :  \"$filename\"</info>\n");
            $output->write("<info>Date        :  $fulldate</info>\n");
            $output->write("<info>Action      :  </info>");
            if (isset($data[$hash]['parsed']) && $data[$hash]['parsed']) {
                $output->write("<skip>Skipping...</skip>\n");
                $skippedImages++;
            } else {
                $output->write("<done>Parsing... </done>");
                $timeStart = microtime(true);
                $text = $this->parseImage($path, true);
                $timeEnd = microtime(true);
                $time = $timeEnd - $timeStart;
                $time = round($time, 2);
                $output->write("<done>Done!</done>\n");
                $output->write("<info>Time        :  {$time}s</info>\n");
                $textArray = explode("\n", $text);
                $data[$hash]['filename'] = $filename;
                $data[$hash]['date'] = $fulldate;
                $data[$hash]['parsed'] = true;
                $data[$hash]['text'] = $textArray;
                $processedImages++;
            }
            $output->write("\n");
            $json->saveJSONFile($this->dataFile, $data);
        }

        $output->write("<head>Processed   :  $processedImages</head>\n");
        $output->write("<head>Skipped     :  $skippedImages</head>\n");
        $output->write("<head>Total       :  $totalImages</head>\n");

        return Command::SUCCESS;
    }
}
