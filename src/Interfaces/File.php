<?php

namespace Dvs\FileBundle\Interfaces;

interface File
{
    /**
     * @param string          $filename
     * @param string          $directories
     * @param resource|string $resource
     *
     * @return File
     */
    public static function create($filename, $directories, $resource);

    /**
     * @param string $filename
     * @param string $originalFilename
     * @param string $directories
     *
     * @return File
     */
    public static function createFromExistingFile($filename, $originalFilename, $directories);

    /**
     * @param string          $uuid
     * @param string          $directories
     * @param resource|string $resource
     *
     * @return File
     */
    public static function withUUID($uuid, $directories, $resource);

    /**
     * @return string
     */
    public function getUUID();

    /**
     * @param string $uuid
     */
    public function setUUID($uuid);

    /**
     * @return string
     */
    public function getFilename();

    /**
     * @param string $filename
     */
    public function setFilename($filename);

    /**
     * @return string
     */
    public function getOriginalFilename();

    /**
     * @param string $originalFilename
     */
    public function setOriginalFilename($originalFilename);

    /**
     * @return string
     */
    public function getDirectories();

    /**
     * @param string $directories
     */
    public function setDirectories($directories);

    /**
     * @return resource|string
     */
    public function getResource();

    /**
     * @return resource
     */
    public function getHandler();

    /**
     * @param resource $handler
     */
    public function setHandler($handler);

    /**
     * @return bool
     */
    public function isTemp();

    /**
     * @param bool $temp
     */
    public function setTemp($temp);
}
