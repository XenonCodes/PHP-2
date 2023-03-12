<?php

namespace XenonCodes\PHP2\Tests\Container;

class ClassDependingOnAnother
{
    // Класс с двумя зависимостями
    public function __construct(
        private SomeClassWithoutDependencies $one,
        private SomeClassWithParameter $two,
    ) {
    }
}
