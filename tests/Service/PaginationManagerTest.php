<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\PaginationManager;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;

final class PaginationManagerTest extends TestCase
{
    public function testPagination()
    {
        $paginationManager = new PaginationManager();

        $qb = $this->createMock(QueryBuilder::class);

        $pagination = $paginationManager->generate($qb, 'my_name');

        $this->assertEquals(1, $pagination->pager->getCurrentPage());
        $this->assertEquals([], $pagination->routeParams);
    }

    public function testPaginationSort()
    {
        $paginationManager = new PaginationManager();

        $qb = $this->createMock(QueryBuilder::class);

        $pagination = $paginationManager->generate(queryBuilder: $qb, routeName: 'my_name', sortField: 'test');
        $this->assertEquals('asc', $pagination->sortOrder);

        $pagination = $paginationManager->generate(queryBuilder: $qb, routeName: 'my_name', sortField: 'test',
            sortOrder:                                           'asc');
        $this->assertEquals('asc', $pagination->sortOrder);

        $pagination = $paginationManager->generate(queryBuilder: $qb, routeName: 'my_name', sortField: 'test',
            sortOrder:                                           'desc');
        $this->assertEquals('desc', $pagination->sortOrder);

        $pagination = $paginationManager->generate(queryBuilder: $qb, routeName: 'my_name', sortField: 'test',
            sortOrder:                                           'zzz');
        $this->assertEquals('asc', $pagination->sortOrder);
    }

    public function testPaginationSortWhitelist()
    {
        $paginationManager = new PaginationManager();

        $qb = $this->createMock(QueryBuilder::class);

        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'test',
            sortFieldsWhitelist: ['test']
        );
        $this->assertEquals('test', $pagination->sortField);

        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'a',
            sortFieldsWhitelist: ['b']
        );
        $this->assertEquals(null, $pagination->sortField);

        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'a',
            sortFieldsWhitelist: ['a', 'b']
        );
        $this->assertEquals('a', $pagination->sortField);
    }

    public function testPaginationWhitelistStringAndIntKeys()
    {
        $paginationManager = new PaginationManager();

        $qb         = $this->prepareQueryBuilder('author.name', 'asc');
        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'author',
            sortFieldsWhitelist: ['author' => 'author.name']
        );
        $this->assertEquals('author', $pagination->sortField);

        $qb         = $this->prepareQueryBuilder('post.createdAt', 'desc');
        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'post.createdAt',
            sortOrder:           'desc',
            sortFieldsWhitelist: ['author' => 'author.name', 'post.createdAt']
        );
        $this->assertEquals('post.createdAt', $pagination->sortField);

        $qb         = $this->prepareQueryBuilder(null, 'asc');
        $pagination = $paginationManager->generate(
            queryBuilder:        $qb,
            routeName:           'r',
            sortField:           'author.name',
            sortOrder:           'desc',
            sortFieldsWhitelist: ['author' => 'author.name', 'post.createdAt']
        );
        $this->assertEquals(null, $pagination->sortField);
    }

    private function prepareQueryBuilder(?string $expectedSortFieldQbName, ?string $expectedSortOrder): QueryBuilder
    {
        $qb = $this->createMock(QueryBuilder::class);

        $qb->method('orderBy')->will(
            $this->returnCallback(
                function ($sortFieldQbName, $sortOrder) use ($expectedSortFieldQbName, $expectedSortOrder) {
                    $this->assertEquals($expectedSortFieldQbName, $sortFieldQbName);
                    $this->assertEquals($expectedSortOrder, $sortOrder);

                    return '';
                }
            )
        );

        if(null === $expectedSortFieldQbName) {
            $qb->expects($this->never())->method('orderBy');
        }

        return $qb;
    }
}
