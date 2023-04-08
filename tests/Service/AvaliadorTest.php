<?php

namespace Alura\Leilao\Tests\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;
use Alura\Leilao\Service\Avaliador;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;

    // Preparação do ambiente
    protected function setUp(): void
    {
        $this->leiloeiro =  new Avaliador();
    }

    public function testleilaoVazioNaoPodeSerAvaliado()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Não é possível avaliar leilão vazio');

        $leilao = new Leilao('Fusca Azul');
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');

        $leilao = new Leilao('Fiat 147');
        $leilao->recebeLance(
            new Lance(new Usuario('teste'), 2000)
        );
        $leilao->finaliza();

        $this->leiloeiro->avalia($leilao);
    }

    /**
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorDeveBuscar3MaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $maioresLances = $this->leiloeiro->getMaioresLances();

        self::assertCount(3, $maioresLances);
        self::assertEquals(3000, $maioresLances[0]->getValor());
        self::assertEquals(2500, $maioresLances[1]->getValor());
        self::assertEquals(2000, $maioresLances[2]->getValor());
    }

    /**
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorDeveEncontrarMaiorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $maiorValor = $this->leiloeiro->getMaiorValor();

        self::assertEquals(3000, $maiorValor);
    }

    /**
     * @dataProvider leilaoEmOrdemDecrescente
     * @dataProvider leilaoEmOrdemCrescente
     * @dataProvider leilaoEmOrdemAleatoria
     */
    public function testAvaliadorDeveEncontrarMenorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $menorValor = $this->leiloeiro->getMenorValor();

        self::assertEquals(1700, $menorValor);
    }

    // Montagem de dados
    public static function leilaoEmOrdemDecrescente(): array
    {
        $leilao = new Leilao('Fiat 47 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');
        $mika = new Usuario('Mika');

        $leilao->recebeLance(new Lance($mika, 3000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 1700));

        return [
            'ordem-decrescente' => [$leilao]
        ];
    }

    public static function leilaoEmOrdemCrescente(): array
    {
        $leilao = new Leilao('Fiat 47 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');
        $mika = new Usuario('Mika');

        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($mika, 3000));

        return [
            'ordem-crescente' => [$leilao]
        ];
    }

    public static function leilaoEmOrdemAleatoria(): array
    {
        $leilao = new Leilao('Fiat 47 0KM');

        $maria = new Usuario('Maria');
        $joao = new Usuario('Joao');
        $ana = new Usuario('Ana');
        $mika = new Usuario('Mika');

        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($ana, 1700));
        $leilao->recebeLance(new Lance($mika, 3000));
        $leilao->recebeLance(new Lance($maria, 2500));

        return [
            'ordem-aleatoria' => [$leilao]
        ];
    }
}
