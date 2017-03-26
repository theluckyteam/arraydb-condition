<?php
namespace luckyteam\arraydb;

/**
 * LIKE - Условие
 */
class LikeCondition extends Condition
{
    /**
     * @inheritdoc
     */
    public function execute($model)
    {
        /** @var array $condition */
        $condition = $this->getCondition();
        if ($value = $this->get($model, $this->getAttribute())) {
            return (boolean) preg_match($condition, $value);
        }
        return false;
    }
}
