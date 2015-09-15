<?php
namespace SlimApi\Skeleton;

interface SkeletonInterface
{
    /**
     * @param array $structure
     *
     * @return void
     */
    public function __construct($structure);

    /**
     * Creates the folder/file structure based on the array describing the structure
     *
     * @param string $path
     * @param string $name
     * @param array $structure
     *
     * @return
     */
    public function create($path, $name, $structure = false);
}
