<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\HashCondition;

/**
 * Тест условия на основе хеша
 */
class HashConditionTest extends Unit
{
    /**
     * Тестировать способ строительства условия на основе хеша
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'attribute1' => 'value1', 'attribute2' => 'value2',
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'attribute1' => ['Foo', 'Bzz'], 'attribute2' => 'value2',
            ],
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition instanceof HashCondition);
        }
    }

    /**
     * Тестировать выполнение условия на основе хеша
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'attribute1' => 'Foo',
                'attribute2' => 'Bar',
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'attribute2' => 'Bar',
                'attribute1' => ['Foo', 'Bzz'],
            ],
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition->execute([
                'attribute1' => 'Foo',
                'attribute2' => 'Bar',
                'attribute3' => 'Bzz',
            ]));
            $this->assertFalse($condition->execute([
                'attribute1' => ['Bzz'],
                'attribute3' => 'Fzz',
            ]));
        }
    }
}