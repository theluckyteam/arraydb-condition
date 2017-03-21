<?php
namespace luckyteam\arraydb;

/**
 * Условие отрицающее, переданное на вход условие
 *
 * Example
 * [
 *   'not', ['>', 'attribute1', 1]
 * ]
 */
class NotCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var Condition $condition */
        $condition = $this->getCondition();
        return !$condition->execute($model);
    }
}
