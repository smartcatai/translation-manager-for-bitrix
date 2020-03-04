<?php
/**
 * Project: likee.smartcat
 * Date: 18.10.17
 *
 * @author: Timokhin Maxim <tm@likee.ru>
 */

namespace Smartcat\Connector;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;

class Schema
{

    protected $dataDir;
    protected $baseVersion;
    protected $alters = [];

    /**
     * Schema constructor.
     * @param string $dataDir path to .sql files
     * @param string $alterPattern glob pattern
     * @param string $baseVersion initial version
     * @throws \Exception
     */
    public function __construct($dataDir, $alterPattern = 'alter-*.sql', $baseVersion = '2.0.6')
    {

        if (!file_exists($dataDir) || !is_dir($dataDir)) {
            throw new \Exception('Data dir is not exists');
        }

        $this->dataDir = $dataDir;
        $this->baseVersion = $baseVersion;
        $this->alters = $this->searchAlters($this->dataDir, $alterPattern);

    }

    protected function searchAlters($dir, $pattern)
    {
        $iterator = new \GlobIterator($dir . '/' . $pattern, \FilesystemIterator::KEY_AS_FILENAME);

        $alters = [];

        $regex = str_replace('*', '(.+)', $pattern);

        foreach ($iterator as $file) {
            /**
             * @var \SplFileInfo $file
             */
            $version = preg_replace('/' . $regex . '/', '$1', $file->getFilename());
            $alters[$version] = $file->getFilename();
        }
        ksort($alters);
        return $alters;
    }

    public function getCurrentVersion()
    {
        $moduleId = basename(dirname(dirname(__FILE__)));
        return Option::get($moduleId, 'schema_version', $this->baseVersion);
    }

    public function setCurrentVersion($version)
    {
        $moduleId = basename(dirname(dirname(__FILE__)));
        Option::set($moduleId, 'schema_version', $version);
    }

    public function getLastVersion()
    {
        return end(array_keys($this->alters));
    }

    public function needUpgrade()
    {
        return $this->getLastVersion() > $this->getCurrentVersion();
    }

    public function upgrade()
    {
        $currentVersion = $this->getCurrentVersion();
        foreach ($this->alters as $version => $file) {
            if ($version > $currentVersion) {
                $query = file_get_contents($this->dataDir . '/' . $file);
                $errors = Application::getConnection()->executeSqlBatch($query);
                if (empty($errors)) {
                    $this->setCurrentVersion($version);
                } else {
                    throw new \Exception(implode("\n", $errors));
                    break;
                }
            }
        }
    }

}