<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Models\BreadcrumbItem;
use App\Service\BreadcrumbManager;
use PHPUnit\Framework\TestCase;

final class BreadcrumbManagerTest extends TestCase
{
    public function testGetNodeNotExistsEmpty()
    {
        $bcManager = new BreadcrumbManager(['a' => new BreadcrumbItem('a.name')]);

        $this->assertEqualsCanonicalizing([], $bcManager->list('z'));
    }

    public function testGetItem()
    {
        $bcManager = new BreadcrumbManager(['a' => new BreadcrumbItem('a.name')]);

        $this->assertEqualsCanonicalizing(
            [['name' => 'a', 'text' => 'a.name', 'params' => []]],
            $bcManager->list('a')
        );

        $bcManager = new BreadcrumbManager(
            [
                'a' => new BreadcrumbItem('a.name'),
                'b' => new BreadcrumbItem('b.name'),
            ]
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
            ],
            $bcManager->list('a')
        );
    }

    public function testGetRootItemWithParams()
    {
        $bcManager = new BreadcrumbManager(['root' => new BreadcrumbItem('root.name', null, ['p1' => 1])]);

        $this->assertEqualsCanonicalizing(
            [['name' => 'root', 'text' => 'root.name', 'params' => ['p1' => 1]]],
            $bcManager->list('root')
        );
    }

    public function testGetChild()
    {
        $bcManager = new BreadcrumbManager(
            [
                'a'    => new BreadcrumbItem('a.name'),
                'a_I'  => new BreadcrumbItem('a_I.name', 'a'),
                'a_II' => new BreadcrumbItem('a_II.name', 'a'),
            ]
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_I', 'text' => 'a_I.name', 'params' => []],
            ],
            $bcManager->list('a_I')
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_II', 'text' => 'a_II.name', 'params' => []],
            ],
            $bcManager->list('a_II')
        );
    }

    public function testGetGrandChild()
    {
        $bcManager = new BreadcrumbManager(
            [
                'a'     => new BreadcrumbItem('a.name'),
                'a_I'   => new BreadcrumbItem('a_I.name', 'a'),
                'a_I_1' => new BreadcrumbItem('a_I_1.name', 'a_I'),
                'a_I_2' => new BreadcrumbItem('a_I_2.name', 'a_I'),
            ]
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_I', 'text' => 'a_I.name', 'params' => []],
                ['name' => 'a_I_1', 'text' => 'a_I_1.name', 'params' => []],
            ],
            $bcManager->list('a_I_1')
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_I', 'text' => 'a_I.name', 'params' => []],
                ['name' => 'a_I_2', 'text' => 'a_I_2.name', 'params' => []],
            ],
            $bcManager->list('a_I_2')
        );
    }

    public function testGetLeafMultipleDeepNested()
    {
        $bcManager = new BreadcrumbManager(
            [
                'a'       => new BreadcrumbItem('a.name'),
                'a_I'     => new BreadcrumbItem('a_I.name', 'a'),
                'a_I_1'   => new BreadcrumbItem('a_I_1.name', 'a_I'),
                'a_I_1_x' => new BreadcrumbItem('a_I_1_x.name', 'a_I_1'),
                'a_I_1_y' => new BreadcrumbItem('a_I_1_y.name', 'a_I_1'),
                'a_I_2'   => new BreadcrumbItem('a_II.a_I_2', 'a_I'),
                'a_I_2_x' => new BreadcrumbItem('a_I_2_x.name', 'a_I_2'),
                'a_II'    => new BreadcrumbItem('a_II.name', 'a'),
                'a_II_1'  => new BreadcrumbItem('a_II_1.name', 'a_II'),
            ]
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_I', 'text' => 'a_I.name', 'params' => []],
                ['name' => 'a_I_1', 'text' => 'a_I_1.name', 'params' => []],
                ['name' => 'a_I_1_x', 'text' => 'a_I_1_x.name', 'params' => []],
            ],
            $bcManager->list('a_I_1_x')
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_I', 'text' => 'a_I.name', 'params' => []],
                ['name' => 'a_I_1', 'text' => 'a_I_1.name', 'params' => []],
                ['name' => 'a_I_1_y', 'text' => 'a_I_1_y.name', 'params' => []],
            ],
            $bcManager->list('a_I_1_y')
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_II', 'text' => 'a_II.name', 'params' => []],
                ['name' => 'a_II_1', 'text' => 'a_II_1.name', 'params' => []],
            ],
            $bcManager->list('a_II_1')
        );

        $this->assertEqualsCanonicalizing(
            [
                ['name' => 'a', 'text' => 'a.name', 'params' => []],
                ['name' => 'a_II', 'text' => 'a_II.name', 'params' => []],
            ],
            $bcManager->list('a_II')
        );
    }
}
