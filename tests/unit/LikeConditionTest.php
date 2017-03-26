<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\LikeCondition;

/**
 * Тест LIKE условия
 */
class LikeConditionTest extends Unit
{
    /**
     * Тестировать способ строительства LIKE условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'like', [
                    'attribute' => "/F.o/"
                ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'like', 'attribute', "/F.o/"
            ],
        ];

        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition instanceof LikeCondition);
            $this->assertEquals($condition->getAttribute(), 'attribute');
            $this->assertEquals($condition->getCondition(), "/F.o/");
            $this->assertTrue($condition->getBuilder() instanceof ConditionBuilder);
        }
    }

    /**
     * Тестировать выполнение LIKE условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [
            // Формат, избранный в первоначальной реализации
            [
                'like', [
                    'attribute1' => '/Fo./'
                ],
            ],
            // Формат, реализованный для соответсвия Yii2
            [
                'like', 'attribute1', '/Fo./'
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
