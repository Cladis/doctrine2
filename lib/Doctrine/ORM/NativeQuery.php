<?php

declare(strict_types=1);

namespace Doctrine\ORM;

/**
 * Represents a native SQL query.
 */
final class NativeQuery extends AbstractQuery
{
    /**
     * @var string
     */
    private $sql;

    /**
     * Sets the SQL of the query.
     *
     * @param string $sql
     *
     * @return NativeQuery This query instance.
     */
    public function setSQL($sql)
    {
        $this->sql = $sql;

        return $this;
    }

    /**
     * Gets the SQL query.
     *
     * @return mixed The built SQL query or an array of all SQL queries.
     *
     * @override
     */
    public function getSQL()
    {
        return $this->sql;
    }

    /**
     * {@inheritdoc}
     */
    protected function doExecute()
    {
        $parameters = [];
        $types      = [];

        foreach ($this->getParameters() as $parameter) {
            $name  = $parameter->getName();
            $value = $this->processParameterValue($parameter->getValue());
            $type  = ($parameter->getValue() === $value)
                ? $parameter->getType()
                : Query\ParameterTypeInferer::inferType($value);

            $parameters[$name] = $value;
            $types[$name]      = $type;
        }

        if ($parameters && is_int(key($parameters))) {
            ksort($parameters);
            ksort($types);

            $parameters = array_values($parameters);
            $types      = array_values($types);
        }

        return $this->em->getConnection()->executeQuery($this->sql, $parameters, $types, $this->queryCacheProfile);
    }
}
