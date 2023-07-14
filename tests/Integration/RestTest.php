<?php

namespace Alura\Leilao\Tests\Integration;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testApiDeveRetornarArrayDeLeiloes()
    {
        $resposta = file_get_contents(
            'http://localhost:8000/rest.php'
        );
        self::assertIsArray(json_decode($resposta));
        self::assertStringContainsString(
            '200 OK',
            $http_response_header[0]
        );
    }
}
