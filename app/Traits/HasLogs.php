<?php

namespace App\Traits;

/**
 * Trait HasLogFile
 * @package App\Traits
 * @property bool $logEveryHour
 * Set to true if there are a lot of logs in a day
 * @property string $logDir
 * The directory name to store the log files in
 *
 */
trait HasLogs
{
    /* @var resource | null $logFileHandle */
    private $logFileHandle = null;
    private string $currentLogFile = '';

    public function log($content): void
    {
        // create a separate file for every hour / day
        $logFileName = property_exists($this, 'logEveryHour') && $this->logEveryHour ? date('Y-m-d-H') . '-00.log' : date('Y-m-d') . '.log';
        if ($logFileName != $this->currentLogFile && $this->logFileHandle) {
            fclose($this->logFileHandle);
            $this->logFileHandle = null;
        }

        // open file if not already opened
        if (!$this->logFileHandle) {
            $logDir = $this->logDir ?? "misc";
            $dir = storage_path("logs/{$logDir}/");
            if (!file_exists($dir)) {
                mkdir($dir);
            }

            $this->currentLogFile = $logFileName;
            $this->logFileHandle = fopen($dir . $this->currentLogFile, 'a');
        }

        // log
        $msg = date("Y-m-d H:i:s ============================================\n");
        $msg .= is_string($content) ? $content : var_export($content, true);
        $msg .= "\n\n";
        fwrite($this->logFileHandle, $msg);
    }
}
