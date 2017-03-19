<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее значение на основании ассоциативного массива
 *
 * Пример обозначения условия:
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
        /** @var array $condition */
        $condition = $this->_condition;

        // Если хотя бы одно условие не верно,
        // то считаем что вся группа условий неверна
        foreach ($condition as $attribute => $value) {
            // Условие является классом условия
            if ($value instanceof Condition) {
                if (!$value->execute($model)) {
                    return false;
                }
            // В противном случае считаем что в условие скаляр
            } else {
                if ($this->get($model, $attribute) !== $value) {
                    return false;
                } elseif (!$this->get($model, $attribute)) {
                    return false;
                }
            }
        }
        return true;
    }
}
