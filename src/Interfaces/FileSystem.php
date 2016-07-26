<?php

namespace Dvs\FileBundle\Interfaces;

use Dvs\FileBundle\Exception\FileNotFoundException;
use Dvs\FileBundle\Exception\FileExistsException;
use Dvs\FileBundle\Exception\RootViolationException;

interface FileSystem
{
    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return bool
     */
    public function has($path);

    /**
     * Read a file.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file contents or false on failure.
     */
    public function read($path);

    /**
     * Write a new file.
     *
     * @param string $path     The path of the new file.
     * @param string $contents The file contents.
     * @param array  $config   An optional configuration array.
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function write($path, $contents, array $config = []);

    /**
     * Write a new file from File.
     *
     * @param File $file File object
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function writeFile(File $file);

    /**
     * Update file from File.
     *
     * @param File   $file    File object
     * @param string $content
     *
     * @return bool True on success, false on failure.
     */
    public function appendFile(File $file, $content);

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool True on success, false on failure.
     */
    public function delete($path);

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @throws RootViolationException Thrown if $dirname is empty.
     *
     * @return bool True on success, false on failure.
     */
    public function deleteDir($dirname);

    /**
     * Create a directory.
     *
     * @param string $dirname The name of the new directory.
     * @param array  $config  An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function createDir($dirname, array $config = []);

    /**
     * Copy/Rename file.
     *
     * @param string $filePath File path
     * @param File   $file     File object
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function copyFile($filePath, File $file);

    /**
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function rename($path, $newPath);

    /**
     * Get base path.
     *
     * @return string
     */
    public function getPathPrefix();
}
