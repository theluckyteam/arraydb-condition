<?php

use Codeception\Test\Unit;
use luckyteam\arraydb\ComparisonCondition;
use luckyteam\arraydb\ConditionBuilder;
use luckyteam\arraydb\InCondition;

class ConditionBuilderTest extends Unit
{
    /**
     * Тестировать способ установки метода строительства условия
     */
    public function setConditionClass()
    {
        $builder = new ConditionBuilder([
            'conditionClasses' => [
                'in' => 'FooCondition',
            ],
        ]);
        $builder->setConditionBuilders([
            'in' => function ($operand, $condition, $builder) {
                /** @var ConditionBuilder $builder */
                $class = $builder->prepareConditionClass($operand);
                $this->assertTrue($class == 'FooCondition');

                return new InCondition(reset($condition), $builder);
            },
        ]);
    }

    /**
     * Тестировать способ установки метода строительства условия
     */
    public function testSetConditionBuilder()
    {
        $builder = new ConditionBuilder([
            'conditionBuilders' => [
                'bzz' => function ($operand, $condition, $builder) {
                    return new InCondition(reset($condition), $builder);
                },
            ],
        ]);
        $builder->setConditionBuilders([
            'foo' => function ($operand, $condition, $builder) {
                return new InCondition(reset($condition), $builder);
            },
        ]);
        $conditions = [
            [
                'bzz', [
                    'attribute' => ['Foo', 'Bzz']
                ],
            ],
            [
                'foo', [
                    'attribute' => ['Foo', 'Bzz']
                ],
            ],
            [
                'in', [
                    'attribute' => ['Foo', 'Bzz']
                ],
            ]
        ];
        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition instanceof InCondition);
        }
    }

    /**
     * Тестировать способ установки соответсвия между операторами сравнения
     */
    public function testSetComparisonOperand()
    {
        $builder = new ConditionBuilder([
            'comparisonOperands' => [
                'foo' => ComparisonCondition::EQUAL,
            ],
        ]);
        $builder->setComparisonOperands([
            '>' => ComparisonCondition::EQUAL,
        ]);
        $conditions = [
            [
                'foo', 'attribute', 100
            ],
            [
                '>', 'attribute', 100
            ],
            [
                '=', 'attribute', 100
            ]
        ];
        foreach ($conditions as $condition) {
            $condition = $builder->build($condition);
            $this->assertTrue($condition->execute(['attribute' => 100]));
        }
    }
}
