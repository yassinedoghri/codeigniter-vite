<?php

declare(strict_types=1);

/**
 * This file was copied and adapted from CodeIgniter Shield.
 *
 * @link https://github.com/codeigniter4/shield
 */

namespace CodeIgniterVite\Commands;

use CodeIgniter\CLI\BaseCommand as CodeIgniterBaseCommand;
use CodeIgniter\CLI\Commands;
use Psr\Log\LoggerInterface;

abstract class BaseCommand extends CodeIgniterBaseCommand
{
    protected static ?InputOutput $io = null;

    /**
     * The group the command is lumped under
     * when listing commands.
     *
     * @var string
     */
    protected $group = 'CodeIgniterVite';

    public function __construct(LoggerInterface $logger, Commands $commands)
    {
        parent::__construct($logger, $commands);

        $this->ensureInputOutput();
    }

    /**
     * @internal Testing purpose only
     */
    public static function setInputOutput(InputOutput $io): void
    {
        self::$io = $io;
    }

    /**
     * @internal Testing purpose only
     */
    public static function resetInputOutput(): void
    {
        self::$io = null;
    }

    /**
     * Asks the user for input.
     *
     * @param string       $field      Output "field" question
     * @param list<string>|string $options    String to a default value, array to a list of options (the first option will be the default value)
     * @param array<string>|string $validation Validation rules
     *
     * @return string The user input
     */
    protected function prompt(
        string $field,
        array|string|null $options = null,
        array|string|null $validation = null
    ): string {
        /** @var InputOutput $io */
        $io = self::$io;

        return $io->prompt($field, $options, $validation);
    }

    /**
     * Outputs a string to the cli on its own line.
     */
    protected function write(string $text = '', ?string $foreground = null, ?string $background = null): void
    {
        /** @var InputOutput $io */
        $io = self::$io;

        $io->write($text, $foreground, $background);
    }

    /**
     * Outputs an error to the CLI using STDERR instead of STDOUT
     */
    protected function error(string $text, string $foreground = 'light_red', ?string $background = null): void
    {
        /** @var InputOutput $io */
        $io = self::$io;

        $io->error($text, $foreground, $background);
    }

    protected function ensureInputOutput(): void
    {
        if (! self::$io instanceof \CodeIgniterVite\Commands\InputOutput) {
            self::$io = new InputOutput();
        }
    }
}
