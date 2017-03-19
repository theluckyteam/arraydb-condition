<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее несколько условий, соединенных оператором ИЛИ
 *
 * Пример обозначения условия:
 * ['or', ['>', 'attribute1', 1], ['<', 'attribute2', 2]]
 */
class OrCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function __construct($condition, ConditionBuilder $builder = null)
    {
        foreach ($condition as &$part) {
            $part = $builder->build($part);
        }
        parent::__construct($condition, $builder);
    }

    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var Condition[] $condition */
        $condition = $this->_condition;
        foreach ($condition as $part) {
            if ($part->execute($model)) {
                return true;
            }
        }
        return false;
    }
}
