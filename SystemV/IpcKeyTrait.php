<?php
/**
 * Process Library
 * @author Tao <taosikai@yeah.net>
 */
namespace Slince\Process\SystemV;

trait IpcKeyTrait
{
    /**
     * Generates the ipc key from an existing file and a project identifier
     * @param string $pathname
     * @param string $projectId
     * @return int
     */
    public function generateIpcKey($pathname, $projectId = 'p')
    {
        return ftok($pathname, $projectId);
    }
}