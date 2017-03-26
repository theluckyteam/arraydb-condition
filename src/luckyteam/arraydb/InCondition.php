<?php
namespace luckyteam\arraydb;

/**
 * IN - Условие
 */
class InCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var array $condition */
        $condition = $this->getCondition();
        return array_key_exists(
            $this->get($model, $this->getAttribute()),
            $condition
        );
    }
}
