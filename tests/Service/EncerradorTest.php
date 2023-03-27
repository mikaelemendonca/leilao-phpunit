<?php

namespace Alura\Leilao\Tests\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Encerrador;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;

class LeilaoTest extends TestCase
{
    public function testLeilaoComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $fiat147 = new Leilao(
            'Fiat 147 0Km',
            new \DateTimeImmutable('8 days ago')
        );
        $variant = new Leilao(
            'Variant 0Km',
            new \DateTimeImmutable('10 days ago')
        );

        $leilaoDao = new LeilaoDao();
        $leilaoDao->salva($fiat147);
        $leilaoDao->salva($variant);

        $encerrador = new Encerrador();
        $encerrador->encerra();
    }
}
