#!/usr/bin/env php
<?php
/**
 * PHPUnit
 *
 * Copyright (c) 2010-2011, Sebastian Bergmann <sb@sebastian-bergmann.de>.
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions
 * are met:
 *
 *   * Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *   * Neither the name of Sebastian Bergmann nor the names of his
 *     contributors may be used to endorse or promote products derived
 *     from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE
 * COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT,
 * INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING,
 * BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT
 * LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN
 * ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    PHPUnit_Selenium
 * @author     Jan Sorgalla <jan.sorgalla@dotsunited.de>
 * @copyright  2010-2011 Sebastian Bergmann <sb@sebastian-bergmann.de>
 * @license    http://www.opensource.org/licenses/bsd-license.php  BSD License
 * @link       http://www.phpunit.de/
 * @since      File available since Release 3.5.0
 */

require_once('SymfonyComponents/YAML/sfYamlDumper.php');


// loop through each element in the $argv array
$message = "Usage: pearsauce  [options] COMMAND [options] [COMMAND [options] ...] [args]\n" .
           "\n" .
           "Available commands:\n" .
           "configure      Configure Sauce OnDemand credentials\n" .
           "help           Provide help for individual commands\n";
if(count($argv) >= 3) {
    if($argv[1] == 'help') {
        if($argv[2] == 'help') {
            $message = "help: Provide help for individual commands\n" .
                       "This command prints the program help if no arguments are given. If one or more command names are given as arguments, these arguments are interpreted as a hierachy of commands and the help for the right most command is show.\n" .
                       "\n" .
                       "Usage: pearsauce  help [COMMAND SUBCOMMAND ...]\n";
        } elseif($argv[2] == 'configure') {
            $message = "configure: Configure Sauce OnDemand credentials\n" .
                       "\n" .
                       "Usage: pearsauce configure USERNAME ACCESS_KEY\n";
        }
    } elseif($argv[1] == 'configure' && count($argv) >= 1) {
        $dumper_lol = new sfYamlDumper();
        $config = array('username' => $argv[2], 'access_key' => $argv[3]);
        $yaml = $dumper_lol->dump($config);
        if(!(file_exists($_SERVER['HOME'] . '/.sauce'))) {
            mkdir($_SERVER['HOME'] . '/.sauce');
        }
        file_put_contents($_SERVER['HOME'] . '/.sauce/ondemand.yml', $yaml);
        $message = "Account configured.  You are now ready to run saucy tests.  You feel very hot and saucy.\n";
    }
}

echo $message;
