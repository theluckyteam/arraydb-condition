<?php
namespace luckyteam\arraydb;

/**
 * Условие проверяющее значение на основании функции обратного вызова
 */
abstract class Condition
{
    /**
     * @var ConditionBuilder построитель условий, используемый для строительства более сложных условий
     */
    public $builder;

    /**
     * @var mixed условие, над которым необходимо выполнить проверку
     */
    protected $_condition;

    /**
     * Создать экземпляр условия
     *
     * @param mixed $condition условие, которое следует проверить
     * @param ConditionBuilder $builder построитель условий
     */
    public function __construct($condition, ConditionBuilder $builder = null)
    {
        $this->_condition = $condition;
        $this->builder = $builder;
    }

    /**
     * Выполнить рассчет условия над объектом
     *
     * @param mixed $model объект, над которым следует выполнить проверку условия
     * @return boolean результат рассчета условия
     */
    public abstract function execute($model);

    /**
     * Получить вложенное значение по ключу
     *
     * @param mixed $array объект, откуда следует извлечь значение
     * @param mixed $key ключ к значению
     * @param null $default значение используемое по умолчанию
     * @return mixed|null значение по ключу
     */
    public function get($array, $key, $default = null)
    {
        if ($key instanceof \Closure) {
            return $key($array, $default);
        }
        if (is_array($key)) {
            $lastKey = array_pop($key);
            foreach ($key as $keyPart) {
                $array = $this->get($array, $keyPart);
            }
            $key = $lastKey;
        }
        if (is_array($array) && (isset($array[$key]) || array_key_exists($key, $array)) ) {
            return $array[$key];
        }
        if (($pos = strrpos($key, '.')) !== false) {
            $array = $this->get($array, substr($key, 0, $pos), $default);
            $key = substr($key, $pos + 1);
        }
        if (is_object($array)) {
            // this is expected to fail if the property does not exist, or __get() is not implemented
            // it is not reliably possible to check whether a property is accessible beforehand
            return $array->$key;
        } elseif (is_array($array)) {
            return (isset($array[$key]) || array_key_exists($key, $array)) ? $array[$key] : $default;
        } else {
            return $default;
        }
    }
}
