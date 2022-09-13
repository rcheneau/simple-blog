<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Models\Pagination;
use App\Service\PaginationTwigProcessor;
use Pagerfanta\PagerfantaInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaginationTwigProcessorTest extends TestCase
{
    public function testPaginationProcessorParams()
    {
        $urlGen = $this->createMock(UrlGeneratorInterface::class);
        $processor = new PaginationTwigProcessor($urlGen);
        $pager  = $this->createMock(PagerfantaInterface::class);

        $pagination = new Pagination($pager, 'r', [], null, 'desc');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertEquals('test', $params['title']);
        $this->assertEquals('desc', $params['direction']);
        $this->assertFalse($params['sorted']);

        $pagination = new Pagination($pager, 'r', [], 'test.name', 'asc');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertTrue($params['sorted']);
        $this->assertEquals('asc', $params['direction']);

        $pagination = new Pagination($pager, 'r', [], 'test.name', 'zzz');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertEquals('asc', $params['direction']);
    }

    public function testPaginationProcessorHrefQuery()
    {
        $urlGen = $this->createMock(UrlGeneratorInterface::class);
        $urlGen->method('generate')->will(
            $this->returnCallback(function ($name, $params) {
                return "$name?" . join('&', array_map(fn($k, $v) => "$k=$v", array_keys($params), $params));
            })
        );
        $processor = new PaginationTwigProcessor($urlGen);
        $pager  = $this->createMock(PagerfantaInterface::class);

        $pagination = new Pagination($pager, 'r', [], 'test.name', 'desc');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertStringContainsString('direction=asc', $params['attributes']['href']);
        $this->assertStringNotContainsString('direction=desc', $params['attributes']['href']);

        $pagination = new Pagination($pager, 'r', ['direction' => 'asc'], 'test.name', 'asc');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertStringContainsString('direction=desc', $params['attributes']['href']);
        $this->assertStringNotContainsString('direction=asc', $params['attributes']['href']);

        $pagination = new Pagination($pager, 'r', ['test' => '1'], 'test.name', 'asc');
        $params = $processor->sortable($pagination, 'test', 'test.name');
        $this->assertStringContainsString('direction=desc', $params['attributes']['href']);
        $this->assertStringContainsString('test=1', $params['attributes']['href']);
    }
}
