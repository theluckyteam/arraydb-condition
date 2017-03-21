<?php
namespace luckyteam\arraydb;

/**
 * Условие сравнения
 *
 * Example
 * ['>', 'attribute1', 1]
 */
class ComparisonCondition extends Condition
{
    /**
     * Оператор сравнения равно
     */
    const EQUAL = '=';

    /**
     * Оператор сравнения не равно
     */
    const NOT_EQUAL = '!=';

    /**
     * Оператор сравнения больше
     */
    const MORE = '>';

    /**
     * Оператор сравнения больше или равно
     */
    const MORE_OR_EQUAL = '>=';

    /**
     * Оператор сравнения меньше
     */
    const LESS = '<';

    /**
     * Оператор сравнения меньше или равно
     */
    const LESS_OR_EQUAL = '<=';

    /**
     * @var string операция сравнения
     */
    private $_operation;

    /**
     * @inheritdoc
     */
    public function __construct($operation, $attribute, $condition, ConditionBuilder $builder = null)
    {
        $this->_operation = $operation;
        parent::__construct($attribute, $condition, $builder);
    }

    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        // Если значение атрибута существует
        if ($value = $this->get($model, $this->getAttribute())) {
            $condition = $this->getCondition();
            $operation = $this->getOperation();

            switch ($operation) {
                // Значение атрибута модели равно сравниваемому
                case self::EQUAL:
                    return $value === $condition;

                // Значение атрибута модели не равно сравниваемому
                case self::NOT_EQUAL:
                    return $value !== $condition;

                // Значение атрибута модели больше сравниваемого
                case self::MORE:
                    return $value > $condition;

                // Значение атрибута модели больше или равно сравниваемого
                case self::MORE_OR_EQUAL:
                    return $value >= $condition;

                // Значение атрибута модели меньше сравниваемого
                case self::LESS:
                    return $value < $condition;

                // Значение атрибута модели меньше или равно сравниваемого
                case self::LESS_OR_EQUAL:
                    return $value <= $condition;

                // Если был передан неизвестный оператор сравнения
                default:
                    throw new BuildConditionException('Was not executed because have unknown comparison operator.');
            }

        // Нечего сравнивать, если значение атрибута не существует
        } else {
            return false;
        }
    }

    public function getOperation()
    {
        return $this->_operation;
    }
}
