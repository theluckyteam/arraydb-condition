<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\AndCondition;
use luckyteam\arraydb\ConditionBuilder;

/**
 * Тест BETWEEN условия
 */
class BetweenConditionTest extends Unit
{
    /**
     * Тестировать способ строительства BETWEEN условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'between', [
                    'attribute' => [1, 10]
                ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'between', 'attribute', 1, 10
            ]
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition instanceof AndCondition);
        }
    }

    /**
     * Тестировать выполнение BETWEEN условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'between', [
                    'attribute' => [1, 10]
                ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'between', 'attribute', 1, 10
            ]
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition->execute(['attribute' => 5]));
            $this->assertFalse($condition->execute(['attribute' => 50]));
        }
    }
}