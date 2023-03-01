<?php

namespace Alura\Leilao\Service;

use Alura\Leilao\Model\Leilao;
use Alura\Leilao\Model\Lance;

class Avaliador
{
    private $maiorValor = -INF;
    private $menorValor = INF;
    private $maioresValores = [];

    public function avalia(Leilao $leilao): void
    {
        foreach ($leilao->getLances() as $lance) {
            if ($lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();
            }
            if ($lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }
        }

        $lances = $leilao->getLances();
        usort($lances, function (Lance $lance1, Lance $lance2) {
            return $lance2->getValor() - $lance1->getValor();
        });
        $this->maioresValores = array_slice($lances, 0, 3);
    }

    public function getMaioresLances(): array
    {
        return $this->maioresValores;
    }

    public function getMaiorValor(): float
    {
        return $this->maiorValor;
    }

    public function getMenorValor(): float
    {
        return $this->menorValor;
    }
}

