<?php

namespace Alura\Leilao\Tests\Unit\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Service\Encerrador;
use Alura\Leilao\Service\EnviadorEmail;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Dao\Leilao as LeilaoDao;

class EncerradorTest extends TestCase
{
    private $encerrador;
    private $leilaoFiat147;
    private $leilaoVariant;
    private $enviadorEmail;

    protected function setUp(): void
    {
        $this->leilaoFiat147 = new Leilao(
            'Fiat 147 0Km',
            new \DateTimeImmutable('8 days ago')
        );
        $this->leilaoVariant = new Leilao(
            'Variant 0Km',
            new \DateTimeImmutable('10 days ago')
        );

        $leiloes = [
            $this->leilaoFiat147,
            $this->leilaoVariant
        ];
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
                    [$this->leilaoFiat147],
                    [$this->leilaoVariant]
                )
            );

        $this->enviadorEmail = $this->createMock(
            EnviadorEmail::class
        );
        $this->encerrador = new Encerrador(
            $leilaoDao,
            $this->enviadorEmail
        );
    }

    public function testLeilaoComMaisDeUmaSemanaDevemSerEncerrados()
    {
        $this->encerrador->encerra();

        $leiloes = [
            $this->leilaoFiat147,
            $this->leilaoVariant
        ];

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

    public function testDeveContinuarOProcessamentoAoEncontrarErroAoEnviarEmail()
    {
        $e = new \DomainException('Erro ao enviar e-mail');

        $this->enviadorEmail
            ->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willThrowException($e);

        $this->encerrador->encerra();
    }

    public function testSoDeveEnviarLeilaoPorEmailAposFinalizado()
    {
        // fazendo testes nos argumentos com o willReturnCallback
        // espero que o notificarTerminoLeilao
        // seja executado 2 vezes 
        // com os leiloes finalizados
        $this->enviadorEmail
            ->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                static::assertTrue($leilao->estaFinalizado());
            });

        $this->encerrador->encerra();
    }
}
