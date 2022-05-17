<?php declare(strict_types=1);

namespace Kmi\DdevCommandsCollection\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Class Scripts
 *
 * @author Konrad Michalik <hello@konradmichalik.eu>
 * @package Kmi\DdevCommandsCollection\Composer
 */
class Scripts
{
    /**
     *
     */
    const TYPES = [
        'typo3',
        'symfony',
        'drupal'
    ];

    /**
     *
     */
    const IGNORE_KEYWORDS = [
        '<keep/>',
        '<ignore/>',
        '<custom/>'
    ];

    /**
     * @var Event
     */
    protected static $event;

    /**
     * @var array
     */
    protected static $extra;

    /**
     * @var Composer
     */
    protected static $composer;

    /**
     * @var IOInterface
     */
    protected static $io;

    /**
     * @var Filesystem
     */
    protected static $fs;

    /**
     * @var array
     */
    protected static $config = [
        'ignoreFiles' => []
    ];

    /**
     * Initialize scripts
     *
     * @param Event $event
     * @return int
     * @throws \Exception
     */
    protected static function init(Event $event): int
    {
        /** @var Event event */
        static::$event = $event;
        /** @var Composer composer */
        static::$composer = $event->getComposer();
        /** @var array extra */
        static::$extra = static::$composer->getPackage()->getExtra();
        /** @var IOInterface io */
        static::$io = $event->getIO();
        /** @var Filesystem fs */
        static::$fs = new Filesystem();

        return self::initConfig();
    }

    /**
     * Initialize configuration
     *
     * @return int
     * @throws \Exception
     */
    private static function initConfig(): int
    {
        static::$config['distDir'] = dirname(dirname(__DIR__)) . '/src/CommandsCollection';

        /**
         * Get config from composer.json
         */
        self::checkAppType();
        static::$config['ddevDir'] = static::$composer->getConfig()->get('ddev-dir') ? './' . static::$composer->getConfig()->get('ddev-dir') : './.ddev';
        if (!is_dir(static::$config['ddevDir'])) {
            static::$io->write(sprintf('<fg=red>[DCC]</> DDEV directory "%s" doesn\'t exist', static::$config['ddevDir']));
            return 1;
        }

        /**
         * Get config from config.yaml
         */
        $configFilePath = static::$config['ddevDir'] . '/commands/dcc-config.yaml';
        if (file_exists($configFilePath)) {
            $configFile = Yaml::parse(file_get_contents($configFilePath));
            if (!is_null($configFile)) {
                static::$config = array_merge(static::$config, $configFile);
            }
        }

        return 0;
    }

    /**
     * Check for configured application type
     *
     * @return void
     * @throws \Exception
     */
    private static function checkAppType(): void
    {
        if (!static::$composer->getConfig()->has('dcc-type')) throw new DCCException('Missing composer.json config for "dcc-type"');

        static::$config['appType'] = strtolower(static::$composer->getConfig()->get('dcc-type'));

        if (!in_array(static::$config['appType'], self::TYPES, true)) throw new DCCException(sprintf('App type %s for DCC not known', static::$config['appType']));
    }

    /**
     * Update ddev commands (if necessary)
     *
     * @param Event $event
     * @throws DCCException|\Exception
     */
    public static function updateCommands(Event $event): void
    {
        $statusCode = static::init($event);
        if (!$statusCode) {
            static::copyFiles();
        }
    }

    /**
     * Copy the files, which are necessary for GitLab Pages in the git root directory
     */
    protected static function copyFiles()
    {
        static::$io->write('<fg=cyan>[DCC]</> Copy <options=bold>DDEV</> command files to project', false);
        $countCopied = 0;

        /**
         * Copy initial general files
         */
        static::$fs->mirror(
            static::$config['distDir'] . '/general/initial',
            static::$config['ddevDir'] . '/commands',
            null,
            ['override' => false]
        );

        /**
         * Copy static general files
         */
        static::$fs->mirror(
            static::$config['distDir'] . '/general/static',
            static::$config['ddevDir'] . '/commands',
            null,
            ['override' => true]
        );

        /**
         * Check for custom commands to ignore
         */
        $commandsPath = static::$config['ddevDir'] . '/commands/';
        $files = glob($commandsPath . '*/dcc-*');
        // add config file
        static::$config['ignoreFiles'][] = $commandsPath . 'dcc-config.sh';
        foreach($files as $filename) {
            if(is_file($filename)) {
                $fileContent = file_get_contents($filename);
                $shouldFileBeIgnored = false;
                foreach (self::IGNORE_KEYWORDS as $keyword) {
                    $shouldFileBeIgnored = (bool)strpos($fileContent, $keyword);
                    if ($shouldFileBeIgnored) break;
                }
                if ($shouldFileBeIgnored) {
                    static::$config['ignoreFiles'][] = str_replace($commandsPath, '', $filename);
                }
            }
        }


        /**
         * Copy command files
         */
        $distCommands = static::$config['distDir'] . '/' . static::$config['appType'] . '/';
        // Process all app type specific command files
        $files = glob($distCommands . '*/dcc-*');
        // add config file
        $files[] = $distCommands . 'dcc-config.sh';
        foreach($files as $fullPathFilename) {
            $relativePathFilename = str_replace($distCommands, '', $fullPathFilename);
            // Check for ignored files
            if (is_null(static::$config['ignoreFiles']) || !in_array($relativePathFilename, static::$config['ignoreFiles'])) {
                $targetFilePath = static::$config['ddevDir'] . '/commands/' . $relativePathFilename;
                // Overwrite/copy file
                static::$fs->copy(
                    $fullPathFilename,
                    $targetFilePath,
                    true
                );
                $countCopied++;
                // Extend files with current app version
                $fileContents = file_get_contents($targetFilePath);
                $fileContents = str_replace("<version/>",static::getVersion(),$fileContents);
                file_put_contents($targetFilePath,$fileContents);
            }
        }

        /**
         * Console info
         */
        $countIgnored = is_null(static::$config['ignoreFiles']) ? 0 : count(static::$config['ignoreFiles']);
        $infoIgnored = is_null(static::$config['ignoreFiles']) ? '' : implode(', ', static::$config['ignoreFiles']);

        $infoMessage = "<fg=green>$countCopied</> file(s) copied";
        if ($countIgnored) {
            $infoMessage .= ", <fg=yellow>$countIgnored</> file(s) ignored: $infoIgnored";
        }

        static::$io->write(" ($infoMessage)");
    }

    /**
     * Get own package version
     *
     * @return string
     */
    protected static function getVersion(): string
    {
        $composerFile = dirname(dirname(__DIR__)) . '/composer.json';
        return (string)\json_decode(file_get_contents($composerFile),true)['version'];
    }
}
