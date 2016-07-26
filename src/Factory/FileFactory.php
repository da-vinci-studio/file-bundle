<?php

namespace Dvs\FileBundle\Factory;

use Dvs\FileBundle\Exception\FileNotFoundException;
use Dvs\FileBundle\File;
use Dvs\FileBundle\Interfaces\FileSystem as FileSystemInterface;
use Dvs\UUIDGenerator\Interfaces\UUIDGenerator;
use Dvs\PathGenerator\Interfaces\PathGenerator;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileFactory
{
    const FILE_CHUNK_SIZE = 1024000;

    /**
     * @var FileSystemInterface
     */
    private $filesystem;

    /**
     * @var UUIDGenerator
     */
    private $UUIDGenerator;

    /**
     * @var PathGenerator
     */
    private $pathGenerator;

    /**
     * @var string
     */
    private $defaultDirectory;

    /**
     * @var string
     */
    private $rootPath = '';

    /**
     * FileFactory constructor.
     *
     * @param FileSystemInterface $filesystem
     * @param UUIDGenerator       $UUIDGenerator
     * @param PathGenerator       $pathGenerator
     * @param string              $defaultDirectory
     */
    public function __construct(
        FileSystemInterface $filesystem,
        UUIDGenerator $UUIDGenerator,
        PathGenerator $pathGenerator,
        $defaultDirectory
    ) {
        $this->filesystem = $filesystem;
        $this->UUIDGenerator = $UUIDGenerator;
        $this->pathGenerator = $pathGenerator;
        $this->defaultDirectory = $defaultDirectory;
        $this->rootPath = $this->filesystem->getPathPrefix();
    }

    /**
     * @param string $filename
     * @param string $directory
     * @param string $mode
     *
     * @return File
     *
     * @throws FileNotFoundException
     */
    public function open($filename, $directory, $mode = 'r')
    {
        $this->createDirIfNotExist($directory);
        $absoluteDirectory = $this->prependRootPath($directory);

        if ($handler = fopen($absoluteDirectory.DIRECTORY_SEPARATOR.$filename, $mode)) {
            $file = File::create($filename, $directory, '');
            $file->setHandler($handler);
        } else {
            throw new FileNotFoundException();
        }

        return $file;
    }

    /**
     * @param File   $file
     * @param string $content
     *
     * @return bool
     */
    public function appendFile(File $file, $content)
    {
        return $this->filesystem->appendFile($file, $content);
    }

    /**
     * @param File $file
     */
    public function close(File $file)
    {
        fclose($file->getHandler());
    }

    /**
     * @param string          $filename
     * @param resource|string $resource
     * @param string          $directory
     *
     * @return File
     */
    public function create($filename, $resource, $directory)
    {
        $file = File::create($filename, $this->createDirs($filename, $directory), $resource);
        $this->filesystem->writeFile($file);

        return $file;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string       $directory
     *
     * @return File|bool
     */
    public function createFromUploadedFile(UploadedFile $uploadedFile, $directory)
    {
        $file = File::createFromExistingFile(
            $uploadedFile->getFilename(),
            $uploadedFile->getClientOriginalName(),
            $this->createDirs(
                $uploadedFile->getFilename().$uploadedFile->getClientOriginalName(),
                $directory
            )
        );

        $movedFile = $uploadedFile->move(
            $this->rootPath.$file->getDirectories(), $uploadedFile->getFilename());

        return $movedFile ? $file : false;
    }

    /**
     * @param string          $filename
     * @param resource|string $resource
     * @param string          $directory
     *
     * @return File
     *
     * @throws \Dvs\UUIDGenerator\Exception\UUIDGeneratorException
     */
    public function createWithUUID($filename, $resource, $directory)
    {
        $uuid = $this->UUIDGenerator->generateFromUrl($filename);
        $file = File::withUUID($uuid, $this->createDirs($uuid, $directory), $resource);
        $this->filesystem->writeFile($file);

        return $file;
    }

    /**
     * @param $uuid
     * @param $directory
     *
     * @return File
     */
    public function openWithUUID($uuid, $directory)
    {
        $resource = $this->filesystem->read($this->createDirs($uuid, $directory).$uuid);

        $file = File::withUUID($uuid, $this->createDirs($uuid, $directory), $resource);

        return $file;
    }

    /**
     * @param string $url
     * @param string $prefix
     *
     * @return bool|File
     */
    public function createTmpFileFromUrl($url, $prefix)
    {
        $ch = curl_init($url);
        $tmpFile = fopen($tmpFilePath = tempnam(sys_get_temp_dir(), $prefix), 'wb');
        $ch = $this->getCurlOptions($prefix, $ch);

        curl_setopt($ch, CURLOPT_FILE, $tmpFile);

        $result = curl_exec($ch);

        $pathInfo = pathinfo($url);

        curl_close($ch);
        fclose($tmpFile);

        if ($result) {
            $file = File::create($tmpFilePath, '', '', $pathInfo['filename'], $this->checkExtension($pathInfo));
            $file->setTemp(true);

            return $file;
        } else {
            return false;
        }
    }

    /**
     * @param File   $file
     * @param string $name
     *
     * @return File
     *
     * @throws FileNotFoundException
     */
    public function renameFile(File $file, $name)
    {
        $directory = $file->getDirectories().DIRECTORY_SEPARATOR;

        if ($this->filesystem->has($directory.$file->getFilename())) {
            $this->filesystem->rename($directory.$file->getFilename(), $directory.$name);
            $file->setFileName($name);
        } else {
            throw new FileNotFoundException();
        }

        return $file;
    }

    /**
     * @param File   $file
     * @param string $target
     * @param string $name
     *
     * @return File
     *
     * @throws FileNotFoundException
     */
    public function moveFileToUuidDir(File $file, $target, $name)
    {
        if (file_exists($file->getFileName())) {
            $uuid = $this->UUIDGenerator->generateFromUrl($name);
            $newDirectory = $this->createDirs($uuid, $target);

            $this->createDirIfNotExist($newDirectory);

            $newFileName = $newDirectory.DIRECTORY_SEPARATOR.$uuid;
            $file->setDirectories($newDirectory);

            if ($file->isTemp()) {
                $this->renameTempFile($file, $newFileName);
            } else {
                $file = $this->renameFile($file, $uuid);
            }

            $file->setFileName($uuid);
            $file->setUUID($uuid);
        } else {
            throw new FileNotFoundException();
        }

        return $file;
    }

    /**
     * @param string $uuid
     * @param string $directory
     *
     * @return string
     */
    public function createDirs($uuid, $directory)
    {
        return $directory.DIRECTORY_SEPARATOR.$this->getDirectoryFromName($uuid);
    }

    /**
     * @param string     $name
     * @param string     $directory
     * @param bool|false $isUrl
     *
     * @return string
     */
    public function createUUIDirsFromName($name, $directory, $isUrl = false)
    {
        $uuid = $isUrl ? $this->UUIDGenerator->generateFromUrl($name) : $this->UUIDGenerator->generateFromString($name);

        return $this->createDirs($uuid, $directory).DIRECTORY_SEPARATOR.$uuid;
    }

    /**
     * @param File $file
     *
     * @return bool
     */
    public function has(File $file)
    {
        return $this->filesystem->has($file->getDirectories().DIRECTORY_SEPARATOR.$file->getFilename());
    }

    /**
     * @param string $directory
     *
     * @return string
     */
    public function prependRootPath($directory)
    {
        return $this->rootPath.$directory;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    public function getDirectoryFromName($name)
    {
        return $this->pathGenerator->generatePathFromName($name);
    }

    /**
     * @param string $newDirectory
     */
    private function createDirIfNotExist($newDirectory)
    {
        if (!is_dir($this->rootPath.$newDirectory)) {
            mkdir($this->rootPath.$newDirectory, 0755, true);
        }
    }

    /**
     * @param string   $prefix
     * @param resource $ch
     *
     * @return mixed
     */
    private function getCurlOptions($prefix, $ch)
    {
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 50);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
        curl_setopt($ch, CURLOPT_COOKIEJAR, sys_get_temp_dir().DIRECTORY_SEPARATOR.$prefix.'tmpCookie.txt');
        curl_setopt($ch, CURLOPT_COOKIEFILE, sys_get_temp_dir().DIRECTORY_SEPARATOR.$prefix.'tmpCookie.txt');
        curl_setopt($ch, CURLOPT_USERAGENT, $this->getAgent());

        return $ch;
    }

    /**
     * @return string
     */
    private function getAgent()
    {
        return 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12';
    }

    /**
     * @param $pathInfo
     *
     * @return string
     */
    private function checkExtension($pathInfo)
    {
        return !empty($pathInfo['extension']) ? $pathInfo['extension'] : '';
    }

    /**
     * @param File   $file
     * @param string $newFileName
     */
    private function renameTempFile(File $file, string $newFileName)
    {
        $fullPath = $this->prependRootPath($newFileName);
        rename($file->getFileName(), $fullPath);
        chmod($fullPath, 0775);
    }
}
