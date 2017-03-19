<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\AndCondition;
use luckyteam\arraydb\ConditionBuilder;

/**
 * Тест AND условия
 */
class AndConditionTest extends Unit
{
    /**
     * Тестировать способ строительства AND условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'and', [
                '>=', 'attribute1' , 1
            ], [
                'attribute2' => 'value2'
            ]
        ]);
        $this->assertTrue($condition instanceof AndCondition);
    }

    /**
     * Тестировать выполнение AND условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'and', [
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