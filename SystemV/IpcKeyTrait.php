<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\RuntimeException;

trait IpcKeyTrait
{
    /**
     * Generates the ipc key from an existing file and a project identifier
     * @param string|int $pathname
     * @param string $projectId
     * @return int
     */
    public function generateIpcKey($pathname, $projectId = 'p')
    {
        if (is_numeric($pathname)) {
            return $pathname;
        }
        if (!file_exists($pathname) && !touch($pathname)) {
            throw new RuntimeException(sprintf("Cannot create the files [%s]", $pathname));
        }
        return ftok($pathname, $projectId);
    }
}
