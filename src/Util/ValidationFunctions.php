<?php

namespace App\Util;
use Psr\Log\LoggerInterface;

class ValidationFunctions
{

    const INVALID_CHARACTERS = array("@", "#");
    const ERROR_CODES = [
        'SUCCESS' => 0,
        'QUERY_ERROR' => 1,
        'INVALID_CHARACTERS' => 2,
        'PROJECT_NOT_FOUND' => 3,
        'TASK_NOT_FOUND' => 4,
        'NOT_ALLOWED' => 5
    ];

    // Check if any of the strings received contains an invalid character
    // returns the list of invalid strings
    public function checkValidCharacters(...$strings): Array
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
            'invalidStrings' => $invalidStrings,
            'errorMessage' => implode(",", $invalidStrings) . " contains invalid characters"
        );
    }

}