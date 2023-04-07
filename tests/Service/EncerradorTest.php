<?php

namespace Alura\Leilao\Tests\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;

class EncerradorTest extends TestCase
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

        $leiloes = [$fiat147, $variant];
        $leilaoDao = $this->createMock(LeilaoDao::class);
        
        // personalizando o mock
        // $leilaoDao = $this
        //     ->getMockBuilder(LeilaoDao::class)
        //     ->disableOriginalConstructor()
        //     ->getMock();

        // informando para o método o que ele deve retornar
        $leilaoDao
            ->method('recuperarFinalizados')
            ->willReturn($leiloes);
        $leilaoDao
            ->method('recuperarNaoFinalizados')
            ->willReturn($leiloes);

        // verificando se o método é executado duas vezes
        $leilaoDao
            ->expects($this->exactly(2))
            ->method('atualiza')
            ->will(
                $this->returnValueMap(
                    [$fiat147],
                    [$variant]
                )
            );

        $encerrador = new Encerrador($leilaoDao);
        $encerrador->encerra();

        self::assertCount(2, $leiloes);
        self::assertEquals(
            'Fiat 147 0Km',
            $leiloes[0]->recuperarDescricao()
        );
        self::assertEquals(
            'Variant 0Km',
            $leiloes[1]->recuperarDescricao()
        );
    }
}
