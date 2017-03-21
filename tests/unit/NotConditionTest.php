<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\Condition;
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
        $this->assertNull($condition->getAttribute());
        $this->assertTrue($condition->getCondition() instanceof Condition);
        $this->assertTrue($condition->getBuilder() instanceof ConditionBuilder);
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
