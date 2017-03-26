<?php
namespace luckyteam\arraydb;

/**
 * Экземпляр условия
 */
abstract class Condition
{
    /**
     * @var string|array имя атрибута
     */
    private $_attribute;

    /**
     * @var mixed условие к атрибуту
     */
    private $_condition;

    /**
     * @var ConditionBuilder построитель условий
     */
    private $_builder;

    /**
     * Конструктор класса
     *
     * @param mixed $attribute имя атрибута
     * @param mixed $condition условие к атрибуту
     * @param ConditionBuilder $builder построитель условий
     */
    public function __construct($attribute, $condition, ConditionBuilder $builder = null)
    {
        $this->_attribute   = $attribute;
        $this->_condition   = $condition;
        $this->_builder     = $builder;
    }

    /**
     * Выполнить/применить условие
     *
     * @param mixed $model объект, над которым следует выполнить проверку условия
     * @return boolean результат проверки условия
     */
    public abstract function execute($model);

    /**
     * Вернуть имя атрибута
     *
     * @return string|array
     */
    public function getAttribute()
    {
        return $this->_attribute;
    }

    /**
     * Вернуть условие к атрибуту
     *
     * @return mixed
     */
    public function getCondition()
    {
        return $this->_condition;
    }

    /**
     * Вернуть построитель условий
     *
     * @return ConditionBuilder
     */
    public function getBuilder()
    {
        return $this->_builder;
    }

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
