<?php
namespace ShoppingFeed\Sdk\Test\Api\Catalog;

use PHPUnit\Framework\TestCase;
use ShoppingFeed\Sdk;

class CatalogDomainTest extends TestCase
{
    public function testGetCatalog()
    {
        $link      = $this->createMock(Sdk\Hal\HalLink::class);
        $resource  = $this->createMock(Sdk\Hal\HalResource::class);
        $link
            ->expects($this->once())
            ->method('get')
            ->willReturn($resource);

        $instance = new Sdk\Api\Catalog\CatalogDomain($link);

        $this->assertInstanceOf(Sdk\Api\Catalog\CatalogResource::class, $instance->getCatalog());
    }

    public function testAskForClearCache()
    {
        $instance = new Sdk\Api\Catalog\CatalogDomain(
            $this->configureCatalogResource('clearCache')
        );

        $instance->askForClearCache();
    }

    public function testAskForFeedImport()
    {
        $instance = new Sdk\Api\Catalog\CatalogDomain(
            $this->configureCatalogResource('importFeed')
        );

        $instance->askForFeedImport();
    }

    public function testAskForFeedImportWithForce()
    {
        $instance = new Sdk\Api\Catalog\CatalogDomain(
            $this->configureCatalogResource('importFeed', [
                'params'    => [
                    'skipSecurityChecks' => ['products', 'references']
                ]
            ])
        );

        $instance->askForFeedImport('products,references');
    }

    protected function configureCatalogResource($operation, $extra = [])
    {
        $catalogLink   = $this->createMock(Sdk\Hal\HalLink::class);
        $operationLink = $this->createMock(Sdk\Hal\HalLink::class);
        $operationLink
            ->expects($this->once())
            ->method('post')
            ->with(
                array_merge(
                    [
                        'operation' => $operation,
                        'catalogId' => 123,
                    ],
                    $extra
                )
            );

        $resource = $this->createMock(Sdk\Hal\HalResource::class);

        $resource
            ->expects($this->once())
            ->method('getLink')
            ->willReturn($operationLink);
        $resource
            ->method('hasProperty')
            ->with('id')
            ->willReturn(true);
        $resource
            ->expects($this->once())
            ->method('getProperty')
            ->with('id')
            ->willReturn(123);
        $catalogLink
            ->expects($this->once())
            ->method('get')
            ->willReturn($resource);

        return $catalogLink;
    }
}
