<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class SimpleController
{

    // Controller outside of the /api/ doesn't use JWT
    public function number()
    {
        $number = mt_rand(0, 100);

        return new Response(
            '<html><body>Lucky number: ' . $number . '</body></html>'
        );
    }
}