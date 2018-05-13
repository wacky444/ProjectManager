<?php
namespace App\Tests\Util;

use App\Util\ValidationFunctions;
use PHPUnit\Framework\TestCase;

class ValidationFunctionsTest extends TestCase
{


    public function testCheckValidCharacters()
    {
        $validationFunctions = new ValidationFunctions();
        $result = $validationFunctions->checkValidCharacters("string1", "string2");

        // assert that your calculator added the numbers correctly!
        $this->assertTrue($result["isValid"]);
    }
}