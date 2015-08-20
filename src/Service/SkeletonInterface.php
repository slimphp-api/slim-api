<?php

namespace SlimApi\Service;

interface SkeletonInterface
{
    public function __construct($structure);
    public function create($path, $name, $structure = false);
}
