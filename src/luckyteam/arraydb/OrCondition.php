<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее несколько условий, соединенных оператором OR
 *
 * Example
 * [
 *   'or', ['>', 'attribute1', 1], ['<', 'attribute2', 2]
 * ]
 */
class OrCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var Condition[] $conditions */
        $conditions = $this->getCondition();
        foreach ($conditions as $condition) {
            if ($condition->execute($model)) {
                return true;
            }
        }
        return false;
    }
}
