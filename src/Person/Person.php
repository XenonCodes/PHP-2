<?php

namespace XenonCodes\PHP2\Person;

use DateTimeImmutable;

class Person
{
    /**
     * @param Name $name объект Name c именем и фамилией 
     * @param DateTimeImmutable $registeredOn дата создания объекта Person
     */
    public function __construct(
        private Name $name,
        private DateTimeImmutable $registeredOn
    ) {
    }

    public function __toString()
    {
        return $this->name .
            ' (на сайте с ' . $this->registeredOn->format('Y-m-d') . ')';
    }
}
