<?php

namespace Dvs\FileBundle\Adapter\FlySystem;

use League\Flysystem\Filesystem as LeagueFilesystem;
use Dvs\FileBundle\Interfaces\FileSystem as FileSystemInterface;
use League\Flysystem\FileExistsException as FlysystemFileExistsException;
use League\Flysystem\FileNotFoundException as FlysystemFileNotFoundException;
use League\Flysystem\RootViolationException as FlysystemRootViolationException;
use Dvs\FileBundle\Exception\FileNotFoundException;
use Dvs\FileBundle\Exception\FileExistsException;
use Dvs\FileBundle\Exception\RootViolationException;
use Dvs\FileBundle\Interfaces\File;

class Local implements FileSystemInterface
{
    /**
     * @var LeagueFilesystem
     */
    protected $filesystem;

    /**
     * FlySystemAdapter constructor.
     *
     * @param LeagueFilesystem $fileSystem
     */
    public function __construct(LeagueFilesystem $fileSystem)
    {
        $this->filesystem = $fileSystem;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public function has($path)
    {
        return $this->filesystem->has($path);
    }

    /**
     * @param string $path
     * @param string $contents
     * @param array  $config
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function write($path, $contents, array $config = [])
    {
        try {
            return $this->filesystem->write($path, $contents, $config);
        } catch (FlysystemFileExistsException $e) {
            FileExistsException::fileExists();
        }
    }

    /**
     * Copy file.
     *
     * @param string $filePath File path
     * @param File   $file     File object
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function copyFile($filePath, File $file)
    {
        try {
            return $this->filesystem->copy($filePath, $file->getDirectories().DIRECTORY_SEPARATOR.$file->getFileName());
        } catch (FlysystemFileExistsException $e) {
            FileExistsException::fileExists();
        }
    }

    /**
     * @param string $path
     * @param string $newPath
     *
     * @return bool
     */
    public function rename($path, $newPath)
    {
        return $this->filesystem->rename($path, $newPath);
    }

    /**
     * Write a new file from File.
     *
     * @param File $file File object
     *
     * @throws FileExistsException
     *
     * @return bool True on success, false on failure.
     */
    public function writeFile(File $file)
    {
        try {
            return $this->filesystem->write($file->getDirectories().$file->getFileName(), $file->getResource(), []);
        } catch (FlysystemFileExistsException $e) {
            FileExistsException::fileExists();
        }
    }

    /**
     * Update file from File.
     *
     * @param File   $file    File object
     * @param string $content
     *
     * @return bool True on success, false on failure.
     */
    public function appendFile(File $file, $content)
    {
        return fwrite($file->getHandler(), $content);
    }

    /**
     * Read a file.
     *
     * @param string $path The path to the file.
     *
     * @throws FileNotFoundException
     *
     * @return string|false The file contents or false on failure.
     */
    public function read($path)
    {
        try {
            return $this->filesystem->read($path);
        } catch (FlysystemFileNotFoundException $e) {
            FileNotFoundException::fileNotFound();
        }
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @throws FileNotFoundException
     *
     * @return bool True on success, false on failure.
     */
    public function delete($path)
    {
        try {
            return $this->filesystem->delete($path);
        } catch (FlysystemFileNotFoundException $e) {
            FileNotFoundException::fileNotExists();
        }
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @throws RootViolationException Thrown if $dirname is empty.
     *
     * @return bool True on success, false on failure.
     */
    public function deleteDir($dirname)
    {
        try {
            return $this->filesystem->deleteDir($dirname);
        } catch (FlysystemRootViolationException $e) {
            RootViolationException::rootViolation();
        }
    }

    /**
     * Create a directory.
     *
     * @param string $dirname The name of the new directory.
     * @param array  $config  An optional configuration array.
     *
     * @return bool True on success, false on failure.
     */
    public function createDir($dirname, array $config = [])
    {
        return $this->filesystem->createDir($dirname, $config);
    }

    /**
     * Get base path.
     *
     * @return string
     */
    public function getPathPrefix()
    {
        return $this->filesystem->getAdapter()->getPathPrefix();
    }
}
