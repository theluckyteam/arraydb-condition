<?php
namespace luckyteam\arraydb;

/**
 * IN - Условие
 */
class InCondition extends Condition
{
    /**
     * @var string наименование атрибута
     */
    private $_attribute;

    /**
     * Создать экземпляр условия
     *
     * @param mixed $condition условие, которое следует проверить
     * @param ConditionBuilder $builder построитель условий
     */
    public function __construct($condition, ConditionBuilder $builder = null)
    {
        $keys = array_keys($condition);
        $this->_attribute = reset($keys);

        $values = array_values($condition);
        $condition = reset($values);

        parent::__construct(array_flip($condition), $builder);
    }

    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var array $condition */
        $condition = $this->_condition;
        return array_key_exists(
            $this->get($model, $this->_attribute),
            $condition
        );
    }
}
