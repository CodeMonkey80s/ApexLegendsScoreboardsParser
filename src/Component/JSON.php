<?php

namespace App\Component;

use Exception;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;

class JSON
{
    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function loadJSONFile(
        string $path = ''
    ): array {
        if (!is_file($path)) {
            throw new FileNotFoundException("*** ERROR *** File: $path is missing!");
        }
        $data = [];
        $raw = file_get_contents($path);
        if ($raw) {
            try {
                $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
            } catch (Exception $e) {
                throw new RuntimeException("*** ERROR *** Invalid JSON!");
            }
        }

        return $data;
    }

    /**
     * @param string $filename
     * @param array $data
     * @return void
     */
    public function saveJSONFile(
        string $filename,
        array $data
    ): void {
        $result = file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));
        if ($result === false) {
            throw new RuntimeException("*** ERROR *** Saving JSON file failed!");
        }
    }
}
