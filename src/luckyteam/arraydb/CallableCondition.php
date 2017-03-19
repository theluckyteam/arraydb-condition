<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее значение на основании функции обратного вызова
 */
class CallableCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var callable $condition */
        $condition = $this->_condition;
        return call_user_func($condition, $model);
    }
}
