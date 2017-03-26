<?php
namespace luckyteam\arraydb;

/**
 * Построитель условий
 *
 * Класс, используемый для строительства объектов условия
 */
class ConditionBuilder
{
    /**
     * @var array классы соответсвующие условиям
     */
    private $_conditionClasses = [
        'and' => 'luckyteam\arraydb\AndCondition',
        'or' => 'luckyteam\arraydb\OrCondition',
        'in' => 'luckyteam\arraydb\InCondition',
        'not' => 'luckyteam\arraydb\NotCondition',
        'like' => 'luckyteam\arraydb\LikeCondition',
        'comparison' => 'luckyteam\arraydb\ComparisonCondition',
        'callable' => 'luckyteam\arraydb\CallableCondition',
        'hash' => 'luckyteam\arraydb\HashCondition',
    ];

    /**
     * @var array способы строительства условий
     */
    private $_conditionBuilders = [
        'and' => 'LogicalCondition',
        'or' => 'LogicalCondition',
        'in' => 'InCondition',
        'between' => 'BetweenCondition',
        'like' => 'LikeCondition',
        'not' => 'NotCondition',
    ];

    /**
     * @var array операторы сравнения
     */
    private $_comparisonOperands = [
        '=' => ComparisonCondition::EQUAL,
        'equal' => ComparisonCondition::EQUAL,
        '!=' => ComparisonCondition::NOT_EQUAL,
        'not equal' => ComparisonCondition::NOT_EQUAL,
        '>' => ComparisonCondition::MORE,
        'more' => ComparisonCondition::MORE,
        '>=' => ComparisonCondition::MORE_OR_EQUAL,
        'more or equal' => ComparisonCondition::MORE_OR_EQUAL,
        '<' => ComparisonCondition::LESS,
        'less' => ComparisonCondition::LESS,
        '<=' => ComparisonCondition::LESS_OR_EQUAL,
        'less or equal' => ComparisonCondition::LESS_OR_EQUAL,
    ];

    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        if (!empty($config)) {
            foreach ($config as $property => $value) {
                $this->$property = $value;
            }
        }
    }

    /**
     * Условие является функцией обратного вызова
     *
     * @param callable $condition параметр для проверки
     * @return boolean результат проверки
     */
    public function isCallableCondition($condition)
    {
        return is_callable($condition);
    }

    /**
     * Условие является массивом
     *
     * @param array $condition параметр для проверки
     * @return boolean результат проверки
     */
    public function isArrayCondition($condition)
    {
        return is_array($condition);
    }

    /**
     * Условие является массивом
     *
     * @param array $condition параметр для проверки
     * @return boolean результат проверки
     */
    public function isHashCondition($condition)
    {
        return $this->isAssociativeArray($condition);
    }

    /**
     * Условие является условием с операндом
     *
     * @param array $condition параметр для проверки
     * @return boolean результат проверки
     */
    public function isOperandCondition($condition)
    {
        if (count($condition) > 1) {
            return $this->isOperand(reset($condition));
        }

        return false;
    }

    /**
     * Переданное значение является операндом
     *
     * @param array $operand операнд
     * @return boolean результат проверки
     */
    public function isOperand($operand)
    {
        return is_scalar($operand)
            && array_key_exists($operand, $this->_conditionBuilders);
    }

    /**
     * Переданное значение является операндом сравнения
     *
     * @param array $operand операнд
     * @return boolean результат проверки
     */
    public function isComparisonOperand($operand)
    {
        return is_scalar($operand)
            && array_key_exists($operand, $this->_comparisonOperands);
    }

    /**
     * Переданное значение является операндом сравнения
     *
     * @param array $condition операнд
     * @return boolean результат проверки
     */
    public function prepareComparisonOperand($condition)
    {
        $condition[0] = $this->_comparisonOperands[$condition[0]];
        return $condition;
    }

    /**
     * Построить условие
     *
     * @param array|callable $condition условие представленное в виде массива или функции обратного вызова,
     * в соответсвии с которым следует построить объект условия
     *
     * @return Condition построенное условие
     * @throws BuildConditionException если в момент строительства произошла ошибка
     */
    public function build($condition)
    {
        // Условие является функцией обратного вызова
        if ($this->isCallableCondition($condition)) {
            $class = $this->prepareConditionClass('callable');
            return new $class(null, $condition, $this);

        // Условие являющееся массивом рассматриваем в соответсвии соследующими правилами
        } elseif ($this->isArrayCondition($condition)) {
            // Условие является списком простых условий, объединенных операндом AND
            // Например: ['attribute1' => 50, 'attribute2' => 100]
            if ($this->isHashCondition($condition)) {
                return $this->buildHashCondition('hash', $condition, $this);

            // Условие содержит операнд, объединяющий одноместную или многоместную операцию
            // Например: ['in', ['attribute' => 50]]
            } elseif ($this->isOperandCondition($condition)) {
                return $this->buildOperandCondition($condition);

            // Иначе предполаем, что условие является порядковым массивом условие сравнения
            // Например: ['>', 'attribute', 50]
            } else {
                if (count($condition) != 3) {
                    throw new BuildConditionException('Condition is incorrect.');
                }
                $operand = reset($condition);
                if ($this->isComparisonOperand($operand)) {
                    /** @var ComparisonCondition $class */
                    $class = $this->prepareConditionClass('comparison');
                    $condition = $this->prepareComparisonOperand($condition);
                    list($operation, $attribute, $value) = $condition;
                    return new $class($operation, $attribute, $value, $this);
                }
            }
        }

        // Если передано условие, из которого нельзя построить условие
        throw new BuildConditionException('Unknown condition for builder.');
    }

    /**
     * Построить условие по операнду
     *
     * @param array $condition условие для строительства
     * @return Condition построенное условие
     * @throws BuildConditionException в момент строительства произошла ошибка
     */
    protected function buildOperandCondition($condition)
    {
        // Условий, где пассив пуст или содержит только операнд не бывает
        if (count($condition) <= 1) {
            throw new BuildConditionException('Unknown condition for builder.');
        }

        $operand = array_shift($condition);

        // Сообщить, что значение операнда неизвестно
        if (!$this->isOperand($operand)) {
            throw new BuildConditionException('Unknown operand for builder.');
        }

        $method = $this->prepareConditionFunction($operand);

        /** @var Condition $condition */
        $condition = call_user_func($method, $operand, $condition, $this);

        return $condition;
    }

    /**
     * Подготовить метод, используемый для строительства условия
     *
     * @param string $operand операнд, для которого необходимо подобрать функцию
     *
     * @return callable функция обратного вызова
     * @throws BuildConditionException если функцию не удалось подобрать
     */
    protected function prepareConditionFunction($operand)
    {
        // Если функция, используемая для строительства описана
        if ($method = $this->_conditionBuilders[$operand]) {
            if (is_string($method)) {
                $method = 'build' . $method;
                if (!method_exists($this, $method)) {
                    throw new BuildConditionException('Unknown build function.');
                }

                return [$this, $method];
            } elseif (is_callable($method)) {
                return $method;
            }
        }

        // Иначе, убедимся в ее наличии
        throw new BuildConditionException('Unknown build function.');
    }

    /**
     * Построить логическое условие
     *
     * @param string $operand операнд, для которого следует построить условие
     * @param mixed $conditions условие для строительства объекта
     * @param ConditionBuilder $builder построитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildLogicalCondition($operand, $conditions, $builder)
    {
        $class = $this->prepareConditionClass($operand);
        $buildConditions = [];
        foreach ($conditions as $condition) {
            $buildConditions[] = $builder->build($condition);
        }
        return new $class(null, $buildConditions, $builder);
    }

    /**
     * Построить простое условие, состоящее из одной операции
     *
     * @param string $operand операнд, для которого следует построить условие
     * @param mixed $condition условие для строительства объекта
     * @param ConditionBuilder $builder построитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildNotCondition($operand, $condition, $builder)
    {
        if (count($condition) !== 1) {
            throw new BuildConditionException('Condition is incorrect.');
        }
        $class = $this->prepareConditionClass($operand);
        $condition = reset($condition);
        $condition = $builder->build($condition);

        return new $class(null, $condition, $builder);
    }

    /**
     * Построить In - условие
     *
     * @param string $operand операнд, для строительства In условия
     * @param mixed $condition условие, представленное в виде массива
     * @param ConditionBuilder $builder строитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildInCondition($operand, $condition, $builder)
    {
        $count = count($condition);
        // Условие не подходит
        if ($operand != 'in'
                || 1 > $count || $count > 3) {
            throw new BuildConditionException('"In" condition is incorrect.');
        }

        // Формат, избранный в первоначальной реализации
        // ['in' => ['attribute' => ['Foo', 'Bzz']]]
        if ($count === 1) {
            $condition = reset($condition);
            $keys = array_keys($condition);
            $attribute = reset($keys);
            $condition = reset($condition);

        // Формат, реализованный для соответсвия Yii2
        // ['in', 'attribute', ['Foo', 'Bzz']]
        } elseif ($count === 2) {
            $attribute = array_shift($condition);
            $condition = array_shift($condition);
        }

        // Проверить возможность создать экземпляр класса
        if (!isset($attribute)
                || !isset($condition)) {
            throw new BuildConditionException('"In" condition is incorrect.');
        }
        $class = $this->prepareConditionClass($operand);

        return new $class($attribute, array_flip($condition), $builder);
    }

    /**
     * Построить Between - условие
     *
     * @param string $operand операнд, для строительства Between условия
     * @param mixed $condition условие, представленное в виде массива
     * @param ConditionBuilder $builder строитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildBetweenCondition($operand, $condition, $builder)
    {
        $count = count($condition);
        if ($operand == 'between') {
            // Формат, избранный в первоначальной реализации
            // ['between', ['attribute' => [1, 10]]]
            if ($count === 1) {
                $condition = reset($condition);
                $keys = array_keys($condition);
                $attribute = reset($keys);
                if (count($condition[$attribute]) == 2) {
                    $left = array_shift($condition[$attribute]);
                    $right = array_shift($condition[$attribute]);
                    return $builder->build([
                        'and', [
                            '>=', $attribute, $left
                        ], [
                            '<=', $attribute, $right
                        ]
                    ]);
                }

            // Формат, реализованный для соответсвия Yii2
            // ['between', 'attribute', 1, 10]
            } elseif ($count === 3) {
                $attribute = array_shift($condition);
                $left = array_shift($condition);
                $right = array_shift($condition);
                return $builder->build([
                    'and', [
                        '>=', $attribute, $left
                    ], [
                        '<=', $attribute, $right
                    ]
                ]);
            }
        }

        throw new BuildConditionException('"Between" condition is incorrect.');
    }

    /**
     * Построить Hash - условие
     *
     * @param string $operand операнд, для строительства Hash условия
     * @param mixed $conditions условие, представленное в виде массива
     * @param ConditionBuilder $builder строитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildHashCondition($operand, $conditions, $builder)
    {
        /** @var HashCondition $class */
        $class = $this->prepareConditionClass($operand);
        $buildConditions = [];
        foreach ($conditions as $attribute => $condition) {
            if (is_array($condition)) {
                $buildConditions[] = $builder->build([
                    'in', $attribute, $condition
                ]);
            } elseif (is_scalar($condition)) {
                $buildConditions[] = $builder->build([
                    'equal', $attribute, $condition
                ]);
            } else {
                throw new BuildConditionException('"Hash" condition is incorrect.');
            }
        }
        return new $class(null, $buildConditions, $builder);
    }

    /**
     * Построить Like - условие
     *
     * @param string $operand операнд, для строительства Like условия
     * @param mixed $condition условие, представленное в виде массива
     * @param ConditionBuilder $builder строитель условий
     * @return Condition экземпляр условия
     * @throws BuildConditionException
     */
    protected function buildLikeCondition($operand, $condition, $builder)
    {
        // Если массив, представляющий условие не соответсвует ожидаемому
        $count = count($condition);
        if ($operand != 'like'
                || 1 > $count || $count > 3) {
            throw new BuildConditionException('"Like" condition is incorrect.');
        }

        // Формат, избранный в первоначальной реализации
        // ['like' => ['attribute' => 'pattern']]
        if ($count === 1) {
            $condition = reset($condition);
            $keys = array_keys($condition);
            $attribute = reset($keys);
            $condition = reset($condition);

        // Формат, реализованный для соответсвия Yii2
        // ['like', 'attribute', 'pattern']
        } elseif ($count === 2) {
            $attribute = array_shift($condition);
            $condition = array_shift($condition);
        }

        // Проверить возможность создать экземпляр класса
        if (!isset($attribute)
            || !isset($condition)) {
            throw new BuildConditionException('"Like" condition is incorrect.');
        }
        $class = $this->prepareConditionClass($operand);

        return new $class($attribute, $condition, $builder);
    }

    /**
     * Подготовить класс для строительства условия
     *
     * @param string $operand операнд
     * @return string имя класса
     * @throws BuildConditionException
     */
    public function prepareConditionClass($operand)
    {
        if (array_key_exists($operand, $this->_conditionClasses)) {
            return $this->_conditionClasses[$operand];
        }

        throw new BuildConditionException('Unknown operand class.');
    }

    /**
     * Заменить классы соответсвующие условиям
     *
     * @param array $classes соответсвие классов и условий
     */
    public function setConditionClasses(array $classes)
    {
        foreach ($classes as $condition => $class) {
            $this->_conditionClasses[$condition] = $class;
        }
    }

    /**
     * Заменить способы строительства условий
     *
     * @param array $builders соответсвие операндов и методов строительства
     */
    public function setConditionBuilders(array $builders)
    {
        foreach ($builders as $operand => $builder) {
            $this->_conditionBuilders[$operand] = $builder;
        }
    }

    /**
     * Заменить операторы сравнения
     *
     * @param array $operands соответствие операндов операторам условий
     */
    public function setComparisonOperands(array $operands)
    {
        foreach ($operands as $operand => $comparisonOperand) {
            $this->_comparisonOperands[$operand] = $comparisonOperand;
        }
    }

    /**
     * Является ли массив ассоциативным
     *
     * @param mixed $array массив для проверки
     * @param boolean $allStrings все ключи должны быть строками
     * @return boolean результат проверки
     */
    public function isAssociativeArray($array, $allStrings = true)
    {
        if (!is_array($array) || empty($array)) {
            return false;
        }
        if ($allStrings) {
            foreach ($array as $key => $value) {
                if (!is_string($key)) {
                    return false;
                }
            }
            return true;
        } else {
            foreach ($array as $key => $value) {
                if (is_string($key)) {
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * Вернуть значение атрибуту, недоступному из вне
     *
     * @param string $name имя значения
     * @throws BuildConditionException если при возвращении значения возникла ошибка
     */
    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        } elseif (method_exists($this, 'set' . $name)) {
            throw new BuildConditionException('Getting write-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new BuildConditionException('Getting unknown property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * Установить значение атрибуту, недоступному из вне
     *
     * @param string $name имя значения
     * @param mixed $value значение
     * @throws BuildConditionException если при установке значение возникла ошибка
     */
    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new BuildConditionException('Setting read-only property: ' . get_class($this) . '::' . $name);
        } else {
            throw new BuildConditionException('Setting unknown property: ' . get_class($this) . '::' . $name);
        }
    }
}
