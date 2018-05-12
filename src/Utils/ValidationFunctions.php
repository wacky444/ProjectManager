<?php

namespace App\Utils;
use Psr\Log\LoggerInterface;

class ValidationFunctions
{

    const INVALID_CHARACTERS = array("@", "#");

    // Check if any of the strings received contains an invalid character
    // returns the list of invalid strings
    public function checkValidCharacters(LoggerInterface $logger, ...$strings): Array
    {
        $hasOnlyValidCharacters = true;
        $invalidStrings = Array();

        foreach ($strings as $string) {
            // Search for all invalid characters
            foreach (self::INVALID_CHARACTERS as $character) {
                if (stripos($string, $character) !== false) {
                    // Add the wrong string only once
                    if(!in_array($string, $invalidStrings)){
                        $invalidStrings[] = $string;
                    }
                    $hasOnlyValidCharacters = false;
                }

            }
        }

        return Array(
            'isValid' => $hasOnlyValidCharacters,
            'invalidStrings' => $invalidStrings
        );
    }

}