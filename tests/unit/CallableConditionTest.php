<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\CallableCondition;
use luckyteam\arraydb\ConditionBuilder;

/**
 * Тест условия - функции обратного вызова
 */
class CallableConditionTest extends Unit
{
    /**
     * Тестировать способ строительства условия на основе функции обратного вызова
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build(function($row){
            return true;
        });
        $this->assertTrue($condition instanceof CallableCondition);
    }

    /**
     * Тестировать выполнение условия на основе функции обратного вызова
     */
    public function testExecute()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build(function($row){
            return $row['attribute'] == 'Foo';
        });
        $this->assertTrue($condition->execute(['attribute' => 'Foo']));
        $this->assertFalse($condition->execute(['attribute' => 'Bzz']));
    }

}
