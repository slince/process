<?php

declare(strict_types=1);

/*
 * This file is part of the slince/process package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Slince\Process\SystemV;

use Slince\Process\Exception\RuntimeException;

final class IpcKeyUtils
{
    /**
     * Generates the ipc key from an existing file and a project identifier
     * @param string $pathname
     * @param string $projectId
     * @return int
     */
    public static function generate(string $pathname, string $projectId = 'p'): int
    {
        if (!file_exists($pathname) && !touch($pathname)) {
            throw new RuntimeException(sprintf("Cannot create the files [%s]", $pathname));
        }
        return ftok($pathname, $projectId);
    }
}
