<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее несколько условий, соединенных оператором AND
 *
 * Example
 * [
 *    'and', ['>', 'attribute1', 1], ['<', 'attribute2', 2]
 * ]
 */
class AndCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var Condition[] $conditions */
        $conditions = $this->getCondition();
        foreach ($conditions as $condition) {
            if (!$condition->execute($model)) {
                return false;
            }
        }
        return true;
    }
}
