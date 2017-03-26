<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\OrCondition;

/**
 * Тест OR условия
 */
class OrConditionTest extends Unit
{
    /**
     * Тестировать способ строительства OR условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'or', [
                '>=', 'attribute1' , 1
            ], [
                'attribute2' => 'value2'
            ]
        ]);
        $this->assertTrue($condition instanceof OrCondition);
        $this->assertNull($condition->getAttribute());
        $this->assertTrue(is_array($condition->getCondition()));
        $this->assertTrue($condition->getBuilder() instanceof ConditionBuilder);
    }

    /**
     * Тестировать выполнение OR условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'or', [
                'attribute3' => 'Foo',
            ], [
                'in', [
                    'attribute1' => ['Foo', 'Bzz']
                ],
            ], [
                'attribute2' => 'Bzz'
            ]
        ]);
        $this->assertTrue($condition->execute([
            'attribute1' => 'Foo',
            'attribute3' => 'Bar',
            'attribute2' => 'Bzz',
        ]));
        $this->assertFalse($condition->execute(['attribute' => 'Bzz']));
    }
}