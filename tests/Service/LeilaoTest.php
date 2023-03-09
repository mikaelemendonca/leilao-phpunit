<?php

namespace Alura\Leilao\Tests\Service;

use PHPUnit\Framework\TestCase;
use Alura\Leilao\Model\Lance;
use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Usuario;

class LeilaoTest extends TestCase
{
    public function testLeilaoNaoDeveReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 lances consecutivos');
        
        $ana = new Usuario('Ana');
        
        $leilao = new Leilao('Fiat 147 0km');
        $leilao->recebeLance(new Lance($ana, 1000));
        $leilao->recebeLance(new Lance($ana, 2000));
    }

    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor mais de 5 lances por leilão');

        $joao = new Usuario('João');
        $maria = new Usuario('Maria');
        
        $leilao = new Leilao('Brasília Amarela');
        $leilao->recebeLance(new Lance($joao, 1000));
        $leilao->recebeLance(new Lance($maria, 1500));
        $leilao->recebeLance(new Lance($joao, 2000));
        $leilao->recebeLance(new Lance($maria, 2500));
        $leilao->recebeLance(new Lance($joao, 300));
        $leilao->recebeLance(new Lance($maria, 3500));
        $leilao->recebeLance(new Lance($joao, 4000));
        $leilao->recebeLance(new Lance($maria, 4500));
        $leilao->recebeLance(new Lance($joao, 5000));
        $leilao->recebeLance(new Lance($maria, 5500));

        $leilao->recebeLance(new Lance($joao, 6000));
    }

    /**
     * @dataProvider GeraLances
     */
    public function testLeilaoDeveReceberLances(
        int $qtdeLances,
        Leilao $leilao,
        array $valores
    ) {
        static::assertCount($qtdeLances, $leilao->getLances());

        foreach ($valores as $key => $valorEsperado) {
            static::assertEquals($valorEsperado, $leilao->getLances()[$key]->getValor());
        }
    }

    public function geraLances()
    {
        $joao = new Usuario('João');
        $maria = new Usuario('Maria');

        $leilaoCom2Lances = new Leilao('Fiat 147 0km');
        $leilaoCom2Lances->recebeLance(new Lance($joao, 1000));
        $leilaoCom2Lances->recebeLance(new Lance($maria, 2000));

        $leilaoCom1Lance = new Leilao('Fusca 1972 0km');
        $leilaoCom1Lance->recebeLance(new Lance($joao, 3000));

        return [
            '1-lance' => [1, $leilaoCom1Lance, [3000]],
            '2-lances' => [2, $leilaoCom2Lances, [1000, 2000]]
        ];
    }
}
