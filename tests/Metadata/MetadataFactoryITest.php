<?php

/*
 * This file is part of the GraphAware Neo4j PHP OGM package.
 *
 * (c) GraphAware Ltd <info@graphaware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace GraphAware\Neo4j\OGM\Tests\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\FileCacheReader;
use GraphAware\Neo4j\OGM\Metadata\EntityPropertyMetadata;
use GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory;
use GraphAware\Neo4j\OGM\Metadata\GraphEntityMetadata;
use GraphAware\Neo4j\OGM\Metadata\NodeEntityMetadata;
use GraphAware\Neo4j\OGM\Tests\Integration\Model\Person;

/**
 * Class MetadataFactoryITest.
 *
 * @group metadata-factory-it
 */
class MetadataFactoryITest extends \PHPUnit_Framework_TestCase
{
    protected $annotationReader;

    /**
     * @var \GraphAware\Neo4j\OGM\Metadata\Factory\GraphEntityMetadataFactory
     */
    protected $entityMetadataFactory;

    public function setUp()
    {
        parent::setUp();
        $mappingDir = getenv('basedir').DIRECTORY_SEPARATOR.'src/Mapping/';
        AnnotationRegistry::registerFile($mappingDir.'/Neo4jOGMAnnotations.php');
        $this->annotationReader = new FileCacheReader(
            new AnnotationReader(),
            getenv('proxydir'),
            true
        );

        $this->entityMetadataFactory = new GraphEntityMetadataFactory($this->annotationReader);
    }

    public function testNodeEntityMetadataIsCreated()
    {
        $entityMetadata = $this->entityMetadataFactory->create(Person::class);
        $this->assertInstanceOf(GraphEntityMetadata::class, $entityMetadata);
        $this->assertInstanceOf(NodeEntityMetadata::class, $entityMetadata);
        $this->assertCount(2, $entityMetadata->getPropertiesMetadata());
        $this->assertInstanceOf(EntityPropertyMetadata::class, $entityMetadata->getPropertyMetadata('name'));
    }

    public function testNewInstancesOfGivenClassCanBeCreate()
    {
        $entityMetadata = $this->entityMetadataFactory->create(Person::class);
        $o = $entityMetadata->newInstance();
        $this->assertInstanceOf(Person::class, $o);
    }

    public function testValueCanBeSetOnInstantiatedObject()
    {
        $entityMetadata = $this->entityMetadataFactory->create(Person::class);
        /** @var Person $o */
        $o = $entityMetadata->newInstance();
        $entityMetadata->getPropertyMetadata('name')->setValue($o, 'John');
        $this->assertEquals('John', $o->getName());
    }
}
