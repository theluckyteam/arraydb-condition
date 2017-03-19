<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\NotCondition;

/**
 * Тест NOT условия
 */
class NotConditionTest extends Unit
{
    /**
     * Тестировать способ строительства NOT условия
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'not', [
                '>=', 'attribute1' , 100
            ]
        ]);
        $this->assertTrue($condition instanceof NotCondition);
    }

    /**
     * Тестировать выполнение NOT условия
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            'not', [
                '>=', 'attribute' , 100
            ]
        ]);
        $this->assertTrue($condition->execute(['attribute' => 50]));
        $this->assertFalse($condition->execute(['attribute' => 100]));
    }
}
