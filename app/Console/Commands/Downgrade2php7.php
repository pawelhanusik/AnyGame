<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Downgrade2php7 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:downgrade2php7 {--nobackup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Downgrades code to be compatible with PHP 7';

    private static $blacklist_path_keywords = ['backup', 'vendor'];
    private static $backup_dir = './app/Console/Commands/downgrade2php7_backup';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (
            !Downgrade2php7::isBackupEmpty()
            && $this->option('nobackup')
        ) {
            echo "Cannot run without backup.\n";
            echo "Please clean backup folder and rerun the command.\n";
            echo "Backup folder path: " . Downgrade2php7::$backup_dir . "\n";
            return 1;
        }
        
        Downgrade2php7::patchDir('./app');
        Downgrade2php7::patchDir('./tests');

        if ($this->option('nobackup')) {
            Downgrade2php7::deleteBackup();
        }

        return 0;
    }

    private static function rsearch($folder, $pattern) {
        // thanks to https://stackoverflow.com/a/54325258
        $dir = new \RecursiveDirectoryIterator($folder);
        $ite = new \RecursiveIteratorIterator($dir);
        $files = new \RegexIterator($ite, $pattern, \RegexIterator::MATCH);
    
        foreach($files as $file) {
            yield $file->getPathName();
        }
    }
    private static function patchDir($dir) {
        $filenames = Downgrade2php7::rsearch($dir, "/.php$/");
        foreach ($filenames as $filename) {
            $ret = Downgrade2php7::patchFile($filename);
            if ($ret === 0) {
                echo "File $filename patched.\n";
            } else if ($ret === -1) {
                // echo "File $filename left untouched.\n";
            } else if ($ret === 1) {
                echo "Error reading file $filename.\n";
            } else {
                echo "Error occurred while patching $filename!\n";
            }
        }
    }
    private static function shouldFileBePatched($filename) {
        foreach (Downgrade2php7::$blacklist_path_keywords as $word) {
            if (strpos($filename, $word) !== false) {
                return false;
            }
        }
    
        $file = fopen($filename, 'r');
        $shouldBePatched = false;
    
        if ($file === false) {
            echo "Cannot open $filename. Not patching this file!\n";
            return false;
        }
        
        while (($line = fgets($file)) !== false) {
            if (Downgrade2php7::shouldLineBePatched($line)) {
                $shouldBePatched = true;
                break;
            }
        }
    
        fclose($file);
        return $shouldBePatched;
    }
    private static function patchFile($filename) {
        if (!file_exists(Downgrade2php7::$backup_dir)) {
            mkdir(Downgrade2php7::$backup_dir);
        }
        
        if (!Downgrade2php7::shouldFileBePatched($filename)) {
            return -1;
        }
    
        if ($filename[0] == '.' && $filename[1] == '/') {
            $filename = substr($filename, 2);
        }
        $backupFilename = $filename;
        $backupFilename = str_replace('/', '_', $backupFilename);
        $backupFilename = str_replace('\\', '_', $backupFilename);
        $backupFilename = Downgrade2php7::$backup_dir . '/' . $backupFilename;
    
        if (!rename($filename, $backupFilename)) {
            echo "Cannot move $filename to $backupFilename. Aborting!\n";
            return 1;
        }
    
        $php8file = fopen($backupFilename, 'r');
        $php7file = fopen($filename, 'w');
    
        if ($php8file === false) {
            echo "Cannot open $backupFilename. Aborting!\n";
            return 1;
        }
        if ($php7file === false) {
            echo "Cannot open $filename. Aborting!\n";
            return 1;
        }
    
        // patch lines
        $buf = '';
        while (($line = fgets($php8file)) !== false) {
            $line = $buf . $line;
            [$controllMessage, $newLine] = Downgrade2php7::patchLine($line);
            if ($controllMessage == 'ok') {
                fputs($php7file, $newLine);
                $buf = '';
            } else if ($controllMessage == 'more') {
                $buf = $line;
            }
        }
    
        fclose($php8file);
        fclose($php7file);
    
        return 0;
    }
    private static function shouldLineBePatched($line) {
        $pos = strpos($line, '?->');
        if ($pos !== false) {
            if (!Downgrade2php7::shouldAbortLinePatch($line, $pos)) {
                return true;
            }
        }

        $pos = strpos($line, 'fn');
        if ($pos !== false) {
            if (!Downgrade2php7::shouldAbortLinePatch($line, $pos)) {
                return true;
            }
        }
    
        return false;
    }
    private static function shouldAbortLinePatch($line, $pos) {
        // check if line is commented
        for ($i = $pos; $i > 0; --$i) {
            if (
                $line[$i] == '/'
                && $line[$i - 1] == '/'
            ) {
                return true;
            }
        }
        
        // check if its string '
        $isString = false;
        for ($i = 0; $i < $pos; ++$i) {
            if ($line[$i] == "'") {
                $isString = !$isString;
            }
        }
        if ($isString) {
            return true;
        }
    
        // check if its string "
        $isString = false;
        for ($i = 0; $i < $pos; ++$i) {
            if ($line[$i] == '"') {
                $isString = !$isString;
            }
        }
        if ($isString) {
            return true;
        }
    
        return false;
    }
    private static function patchLine($line) {
        $pos = strpos($line, '?->');
        if ($pos !== false) {
            // ?->
            if (!Downgrade2php7::shouldAbortLinePatch($line, $pos)){
                $startingChars = [' ', '$'];
                $endingChars = [' ', ';'];

                $stStart = $pos;
                $stEnd = $pos;
                for (; $stStart >= 0; --$stStart) {
                    if (in_array($line[$stStart], $startingChars)) {
                        break;
                    }
                }
                for (; $stEnd < strlen($line); ++$stEnd) {
                    if (in_array($line[$stEnd], $endingChars)) {
                        break;
                    }
                }

                $line = substr($line, 0, $stStart)
                    . '($gameComponent->editor === null ? null : '
                    . substr($line, $stStart, $pos - $stStart)
                    . substr($line, $pos + 1, $stEnd - $pos - 1)
                    . ')'
                    . substr($line, $stEnd)
                ;
            }
        }
        $pos = strpos($line, 'fn');
        if ($pos !== false) {
            // fn
            $shouldAbort = false;
            for($i = $pos; $i >= 0; --$i) {
                if (
                    $line[$i] == '('
                    || $line[$i] == ' '
                ) {
                    $shouldAbort = false;
                    break;
                }
                if ($line[$i] == '$') {
                    $shouldAbort = true;
                    break;
                }
            }
            if (!$shouldAbort && !Downgrade2php7::shouldAbortLinePatch($line, $pos)){
                $stStart = $pos;
                $fnPos = $pos;
                $arrowPos = $pos;
                $stEnd = null;
                
                for (; $stStart >= 0; --$stStart) {
                    if ($line[$stStart] == '(') {
                        break;
                    }
                }
                if ($line[$stStart] != '(') {
                    return ['more', ''];
                }
                for (; $arrowPos < strlen($line) - 1; ++$arrowPos) {
                    if (
                        $line[$arrowPos] == '='
                        && $line[$arrowPos + 1] == '>'
                    ) {
                        break;
                    }
                }
                if ($arrowPos >= strlen($line)) {
                    return ['more', ''];
                }
                $stEnd = $arrowPos;
                $braceCount = 1;
                for (; $stEnd < strlen($line); ++$stEnd) {
                    if ($line[$stEnd] ==  '(') {
                        ++$braceCount;
                    }
                    if ($line[$stEnd] ==  ')') {
                        --$braceCount;
                    }
                    if ($braceCount <= 0) {
                        break;
                    }
                }
                if ($stEnd >= strlen($line)) {
                    return ['more', ''];
                }

                $useVarsArr = [];
                $varBeforeChars = ['=', ','];
                $varEndChars = [' ', ';', '[', ')', '-'];
                $innerLines = explode("\n", substr($line, $arrowPos + 2, $stEnd - $arrowPos - 2));
                foreach ($innerLines as $l) {
                    $varStartPos = null;
                    // start at 1, since if there's a $ at the beggining of the line
                    // - probably it's declaration of a new variable
                    for ($i = 1; $i < strlen($l); ++$i) {
                        if ($varStartPos === null) {
                            if ($l[$i] == '$') {
                                $isNewVar = true;
                                for ($j = $i - 1; $j >= 0; --$j) {
                                    if (in_array($l[$j], $varBeforeChars)) {
                                        $isNewVar = false;
                                        break;
                                    }
                                    if ($l[$j] != ' ') {
                                        $isNewVar = true;
                                        break;
                                    }
                                }
                                if (!$isNewVar) {
                                    $varStartPos = $i;
                                }
                            }
                        } else {
                            if (in_array($l[$i], $varEndChars)) {
                                $varname = substr($l, $varStartPos, $i - $varStartPos);
                                if (!in_array($varname, $useVarsArr)) {
                                    $useVarsArr[] = $varname;
                                }
                                $varStartPos = null;
                            }
                        }
                    }
                }
                $useVars = '';
                foreach ($useVarsArr as $varname) {
                    $useVars .= ', ' . $varname;
                }
                // delete starting ", "
                if (strlen($useVars) > 0) {
                    $useVars = substr($useVars, 2);
                }

                $line = substr($line, 0, $stStart + 1)
                    . 'function'
                    . substr($line, $fnPos + 2, $arrowPos - $fnPos - 2)
                    . ((strlen($useVars) > 0) ? ('use (' . $useVars . ')') : '')
                    . '{ return'
                    . substr($line, $arrowPos + 2, $stEnd - $arrowPos - 2)
                    . ';}'
                    . substr($line, $stEnd)
                ;
            }
        }
    
        return ['ok', $line];
    }
    private static function isBackupEmpty() {
        foreach (glob(Downgrade2php7::$backup_dir . '/*.php') as $filename) {
            return false;
        }
        return true;
    }
    private static function deleteBackup() {
        foreach (glob(Downgrade2php7::$backup_dir . '/*.php') as $filename) {
            unlink($filename);
        }
        rmdir(Downgrade2php7::$backup_dir);
    }
}
