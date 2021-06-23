<?php

/*
 * UserFrosting Framework (http://www.userfrosting.com)
 *
 * @link      https://github.com/userfrosting/framework
 * @copyright Copyright (c) 2013-2021 Alexander Weissman, Louis Charette, Jordan Mele
 * @license   https://github.com/userfrosting/framework/blob/master/LICENSE.md (MIT License)
 */

namespace UserFrosting\Testing;

use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Trait that provide `runCommand` method to test Bakery commands.
 * Requires the CI (\UserFrosting\Testing\TestCase).
 */
trait BakeryCommandTester
{
    /**
     * Execute Bakery command.
     *
     * @codeCoverageIgnore
     *
     * @param string $command     Commands to register.
     * @param array  $input       An array of command arguments and options
     * @param array  $userInputAn Array of strings representing each input passed to the command input stream
     *
     * @return CommandTester
     */
    protected function runCommand(
        string $command,
        array $input = [],
        array $userInput = []
    ): CommandTester {

        // Fail if no $ci
        if (!isset($this->ci) || !($this->ci instanceof ContainerInterface)) {
            throw new \Exception('runCommand requires access to the ContainerInterface in `$this->ci`.');
        }

        // Create app and add command instance from CI to App
        $app = new Application();
        $instance = $this->ci->get($command);
        $app->add($instance);

        // Add the command to the input to create the execute argument
        $executeDefinition = array_merge([
            'command' => $instance->getName(),
        ], $input);

        // Create command tester
        $commandTester = new CommandTester($instance);

        // Set user inpu
        if (!empty($userInput)) {
            $commandTester->setInputs($userInput);
        }

        // Execute command
        $commandTester->execute($executeDefinition);

        return $commandTester;
    }
}
