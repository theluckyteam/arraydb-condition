<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ComparisonCondition;
use luckyteam\arraydb\ConditionBuilder;

/**
 * Тест условия сравнения
 */
class ComparisonConditionTest extends Unit
{
    /**
     * Тестировать способ строительства условия сравнения
     */
    public function testBuildClass()
    {
        $builder = new ConditionBuilder();
        $condition = $builder->build([
            '>=', 'attribute1' , 100
        ]);
        $this->assertTrue($condition instanceof ComparisonCondition);
    }

    /**
     * Тестировать выполнение условия сравнения - Equal
     */
    public function testEqualExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '=', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'equal', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 100]));
            $this->assertFalse($condition->execute(['attribute' => 50]));
        }
    }

    /**
     * Тестировать выполнение условия сравнения - Not Equal
     */
    public function testNotEqualExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '!=', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'not equal', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 50]));
            $this->assertFalse($condition->execute(['attribute' => 100]));
        }
    }

    /**
     * Тестировать выполнение условия сравнения - More
     */
    public function testMoreExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '>', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'more', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 150]));
            $this->assertFalse($condition->execute(['attribute' => 100]));
        }
    }

    /**
     * Тестировать выполнение условия сравнения - More or Equal
     */
    public function testMoreOrEqualExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '>=', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'more or equal', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 100]));
            $this->assertFalse($condition->execute(['attribute' => 50]));
        }
    }

    /**
     * Тестировать выполнение условия сравнения - Less
     */
    public function testLessExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '<', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'less', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 50]));
            $this->assertFalse($condition->execute(['attribute' => 100]));
        }
    }

    /**
     * Тестировать выполнение условия сравнения - Less or Equal
     */
    public function testLessOrEqualExecute()
    {
        $builder = new ConditionBuilder();
        $conditions = [];
        $conditions[] = $builder->build([
            '<=', 'attribute' , 100
        ]);
        $conditions[] = $builder->build([
            'less or equal', 'attribute' , 100
        ]);
        foreach ($conditions as $condition) {
            $this->assertTrue($condition->execute(['attribute' => 100]));
            $this->assertFalse($condition->execute(['attribute' => 150]));
        }
    }
}
