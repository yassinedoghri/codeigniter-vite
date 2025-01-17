<?php

declare(strict_types=1);

namespace CodeIgniterVite\Config;

class Registrar
{
    /**
     * @return array<mixed>
     */
    public static function View(): array
    {
        return [
            'decorators' => [\CodeIgniterVite\ViteDecorator::class],
        ];
    }
}
