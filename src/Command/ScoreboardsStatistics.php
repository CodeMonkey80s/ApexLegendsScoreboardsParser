<?php

namespace App\Command;

use App\Component\JSON;
use App\Formatter\CustomFormatter;
use App\Formatter\OutputFormatterStyles;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\KernelInterface;

class ScoreboardsStatistics extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:scoreboards-statistics';

    /**
     * @var string
     */
    protected static $defaultDescription = 'Process scoreboards json and prints statistics.';

    /**
     * @var string
     */
    protected string $dataFile = '';

    /**
     * @var string
     */
    protected string $username = '';

    /**
     * @param string $dataFile
     * @param string $username
     */
    public function __construct(
        string $dataFile,
        string $username
    ) {
        $this->dataFile = $dataFile;
        $this->username = $username;

        parent::__construct();
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this
            ->setHelp('This command parses scoreboards data file and prints statistics.')
            ->addOption('summary', null, InputOption::VALUE_NONE, 'Prints only summary.');
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
        if (empty($this->username)) {
            throw new InvalidArgumentException("*** ERROR *** Username: '$this->username' is empty!");
        }
        if (!file_exists($this->dataFile)) {
            throw new InvalidArgumentException("*** ERROR *** Data File: '$this->dataFile' does not exists!");
        }
        $formatter = new OutputFormatter(
            true,
            OutputFormatterStyles::getStyles()
        );
        $output->setFormatter($formatter);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {
        $optionSummary = $input->getOption('summary');
        if (!$optionSummary) {
            $output->write("<head>Parsing Scoreboards Data...</head>\n\n");
        }

        $json = new JSON();
        $data = $json->loadJSONFile($this->dataFile);

        $totalKills = 0;
        $totalAssists = 0;
        $totalKnocks = 0;
        $totalDamage = 0;
        $totalReviveGiven = 0;
        $totalRespawnGiven = 0;

        $totalImages = count($data);
        if ($totalImages == 0) {
            $output->write("<fail>*** ERROR *** Data file is empty!</fail>\n\n");

            return Command::FAILURE;
        }

        $details = [];
        for ($i = 0; $i <= 45; $i++) {
           $details[$i] = [
               'kills' => 0,
               'assists' => 0,
               'knocks' => 0,
               'damage' => 0,
               'revives' => 0,
               'respawns' => 0,
           ];
        }

        $dates = [];

        $processed = 0;
        foreach ($data as $hash => $row) {
            $processed++;
            $filename = $row['filename'];
            if (!$optionSummary) {
                $output->write("<info>ID          :  $processed</info>\n");
                $output->write("<info>Hash        :  $hash </info>\n");
                $output->write("<info>Filename    :  \"$filename\"</info>\n");
            }
            $i = 0;
            foreach ($row['text'] as $text) {
                if ($text == $this->username) {
                    $stats = explode('/', $row['text'][$i + 2]);
                    $statDamage = $row['text'][$i + 4];
                    $statReviveGiven = $row['text'][$i + 8];
                    $statRespawnGiven = $row['text'][$i + 10];
                    $statKills = $stats[0];
                    $statAssists = $stats[1];
                    $statKnocks = $stats[2];
                    $totalDamage += $statDamage;
                    $totalKills += $statKills;
                    $totalAssists += $statAssists;
                    $totalKnocks += $statKnocks;
                    $totalReviveGiven += $statReviveGiven;
                    $totalRespawnGiven += $statRespawnGiven;
                    if (!$optionSummary) {
                        $output->write("<info>Statistics  :  </info><head>$statKills $statAssists $statKnocks $statDamage</head>\n");
                    }
                    $details[$statKills]['kills']++;
                    $details[$statAssists]['assists']++;
                    $details[$statKnocks]['knocks']++;
                    $details[$statReviveGiven]['revives']++;
                    $details[$statRespawnGiven]['respawns']++;
                    $index = round($statDamage / 100);
                    if (isset($details[$index]['damage'])) {
                        $details[$index]['damage']++;
                    }
                }
                $i++;
            }
            $dates[] = $row['date'];
            if (!$optionSummary) {
                $output->write("\n");
            }
        }

        foreach($dates as $key => $date) {
            if(isset($dates[($key+1)]))
                $intervals[] = abs(strtotime($date) - strtotime($dates[($key+1)]));
        }
        $averageTime = array_sum($intervals) / count($intervals);
        $averageTime = $averageTime / 3600;

        $averageDamage = number_format($totalDamage / $processed, 2, '.', '');
        $averageKills = number_format($totalKills / $processed, 2);
        $averageAssists = number_format($totalAssists / $processed, 2);
        $averageKnocks = number_format($totalKnocks / $processed, 2);
        $averageRevives = number_format($totalReviveGiven / $processed, 2);
        $averageRespawns = number_format($totalRespawnGiven / $processed, 2);

        $table = new Table($output);
        $table->setHeaders(
            [
                "<head>{$this->username}</head>",
                '',
                '<done>Kills</done>',
                '<done>Assists</done>',
                '<done>Knocks</done>',
                '<done>Damage</done>',
                '<done>Revives</done>',
                '<done>Respawns</done>',
                '<done>Wins</done>',
            ]
        );
        $table->setStyle('box-double');
        $tableStyle = $table->getStyle();
        $tableStyle->setPadType(STR_PAD_BOTH);
        $table->setStyle($tableStyle);
        $table->setColumnWidths(array_fill(0, 9, 12));
        $table->setRows(
            [
                [
                    '',
                    'total',
                    $totalKills,
                    $totalAssists,
                    $totalKnocks,
                    $totalDamage,
                    $totalReviveGiven,
                    $totalRespawnGiven,
                    $processed,
                ],
                new TableSeparator(),
                [
                    '',
                    'average',
                    $averageKills,
                    $averageAssists,
                    $averageKnocks,
                    $averageDamage,
                    $averageRevives,
                    $averageRespawns,
                    round($averageTime, 2) . 'h'
                ],
                new TableSeparator(),
            ]
        );
        $i = 0;
        foreach ($details as $detail) {
            $table->setRow(
                $i + 7,
                [
                    $i,
                    $i * 100,
                    $detail['kills'] > 0 ? $detail['kills'] : '<dark>-</dark>',
                    $detail['assists'] > 0 ? $detail['assists'] : '<dark>-</dark>',
                    $detail['knocks'] > 0 ? $detail['knocks'] : '<dark>-</dark>',
                    $detail['damage'] > 0 ? $detail['damage'] : '<dark>-</dark>',
                    $detail['revives'] > 0 ? $detail['revives'] : '<dark>-</dark>',
                    $detail['respawns'] > 0 ? $detail['respawns'] : '<dark>-</dark>',
                    '<dark>-</dark>',
                ]
            );
            $i++;
        }
        $table->render();

        return Command::SUCCESS;
    }
}
