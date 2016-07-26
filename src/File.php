<?php

namespace Dvs\FileBundle;

use Dvs\FileBundle\Interfaces\File as FileInterface;

class File implements FileInterface
{
    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var string
     */
    protected $filename = null;

    /**
     * @var string
     */
    protected $originalFilename = null;

    /**
     * @var string
     */
    protected $directories;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var resource|string
     */
    protected $resource;

    /**
     * @var resource
     */
    protected $handler;

    /**
     * @var bool
     */
    protected $temp = false;

    /**
     * @param string          $filename
     * @param string          $directories
     * @param resource|string $resource
     * @param string|null     $originalFilename
     * @param string|null     $extension
     *
     * @return File
     */
    public static function create($filename, $directories, $resource, $originalFilename = null, $extension = null)
    {
        $file = new self();

        $file->filename = $filename;
        $file->directories = $directories;
        $file->resource = $resource;
        $file->originalFilename = $originalFilename;
        $file->extension = $extension;

        return $file;
    }

    /**
     * @param string $filename
     * @param string $originalFilename
     * @param string $directories
     *
     * @return File
     */
    public static function createFromExistingFile($filename, $originalFilename, $directories)
    {
        $file = new self();

        $file->filename = $filename;
        $file->originalFilename = $originalFilename;
        $file->directories = $directories;

        return $file;
    }

    /**
     * @param string          $uuid
     * @param string          $directories
     * @param resource|string $resource
     *
     * @return File
     */
    public static function withUUID($uuid, $directories, $resource)
    {
        $file = new self();

        $file->filename = $file->uuid = $uuid;
        $file->directories = $directories;
        $file->resource = $resource;

        return $file;
    }

    /**
     * @return string
     */
    public function getUUID()
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUUID($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
    }

    /**
     * @return string
     */
    public function getOriginalFilename()
    {
        return $this->originalFilename;
    }

    /**
     * @param string $originalFilename
     */
    public function setOriginalFilename($originalFilename)
    {
        $this->originalFilename = $originalFilename;
    }

    /**
     * @return string
     */
    public function getDirectories()
    {
        return $this->directories;
    }

    /**
     * @param string $directories
     */
    public function setDirectories($directories)
    {
        $this->directories = $directories;
    }

    /**
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * @return resource|string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return resource
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param resource $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return bool
     */
    public function isTemp()
    {
        return $this->temp;
    }

    /**
     * @param bool $temp
     */
    public function setTemp($temp)
    {
        $this->temp = $temp;
    }
}
