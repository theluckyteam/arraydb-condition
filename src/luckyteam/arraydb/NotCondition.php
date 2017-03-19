<?php
namespace luckyteam\arraydb;

/**
 * Условие отрицающее, переданное на вход значение условия
 *
 * Пример обозначения условия:
 * ['not', ['>', 'attribute1', 1]]
 */
class NotCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function __construct($condition, ConditionBuilder $builder = null)
    {
        parent::__construct($builder->build($condition), $builder);
    }

    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var Condition $condition */
        $condition = $this->_condition;
        return !$condition->execute($model);
    }
}
