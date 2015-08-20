<?php
namespace SlimApiTest\Service;

use SlimApi\Skeleton\SkeletonService;
use org\bovigo\vfs\vfsStream;
/**
 *
 */
class SkeletonServiceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp() {
        $this->root = vfsStream::setup();
    }

    public function testSimpleDirectoryCreation()
    {
        // should just create the root dir if it doesn't exist and a single file
        $structure = ['foo.txt' => 'bar'];
        $skeletonService = new SkeletonService($structure);
        $skeletonService->create($this->root->url(), 'Baz');
        $this->assertTrue($this->root->hasChild('foo.txt'));
        $this->assertEquals('bar', $this->root->getChild('foo.txt')->getContent());
    }

    public function testNonExistantPathSimpleDirectoryCreation()
    {
        // should just create the root dir if it doesn't exist and a single file
        $structure = ['foo.txt' => 'bar'];
        $skeletonService = new SkeletonService($structure);
        $skeletonService->create($this->root->url().'/fuz', 'Baz');
        $this->assertTrue($this->root->hasChild('fuz/foo.txt'));
        $this->assertEquals('bar', $this->root->getChild('fuz/foo.txt')->getContent());
    }

    public function testComplexDirectoryCreation()
    {
        // should just create the root dir if it doesn't exist and a single file
        $structure = ['one' => ['foo.txt' => 'bar']];
        $skeletonService = new SkeletonService($structure);
        $skeletonService->create($this->root->url(), 'Baz');
        $this->assertTrue($this->root->hasChild('one/foo.txt'));
        $this->assertTrue(is_dir($this->root->url().'/one'));
        $this->assertTrue(is_file($this->root->url().'/one/foo.txt'));
        $this->assertEquals('bar', $this->root->getChild('one/foo.txt')->getContent());
    }
}
