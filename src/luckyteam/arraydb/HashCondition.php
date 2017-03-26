<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее значение на основании ассоциативного массива
 *
 * Example
 * - ['attribute1' => 1, 'attribute1' => 2]
 * - ['attribute1' => ['Foo', 'Bzz'], 'attribute2' => 'value2']
 */
class HashCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var array $conditions */
        $conditions = $this->getCondition();

        // Если хотя бы одно условие не верно,
        // то считаем что вся группа условий неверна
        foreach ($conditions as $attribute => $value) {
            // Условие является классом условия
            if ($value instanceof Condition) {
                if (!$value->execute($model)) {
                    return false;
                }
            // В противном случае считаем что не выполняется
            } else {
                return false;
            }
        }
        return true;
    }
}
