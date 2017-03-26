<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\InCondition;

/**
 * Тест IN условия
 */
class InConditionTest extends Unit
{
    /**
     * Тестировать способ строительства IN условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'in', [
                    'attribute' => ['Foo', 'Bzz']
                ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'in', 'attribute', ['Foo', 'Bzz']
            ],
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition instanceof InCondition);
            $this->assertEquals($condition->getAttribute(), 'attribute');
            $this->assertEquals($condition->getCondition(), ['Foo' => 0, 'Bzz' => 1]);
            $this->assertTrue($condition->getBuilder() instanceof ConditionBuilder);
        }
    }

    /**
     * Тестировать выполнение IN условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'in', [
                'attribute1' => ['Foo', 'Bzz']
            ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'in', 'attribute1', ['Foo', 'Bzz']
            ],
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition->execute([
                'attribute1' => 'Foo',
                'attribute3' => 'Bar',
                'attribute2' => 'Bzz',
            ]));
            $this->assertFalse($condition->execute([
                'attribute1' => 'Bar',
            ]));
        }
    }
}
