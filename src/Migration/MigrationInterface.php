<?php
namespace SlimApi\Migration;

interface MigrationInterface
{
    /**
     * Where any initiation for db service during app setup occurs
     *
     * @param string $directory The directory to init at!
     *
     * @return void
     */
    public function init($directory);
}
