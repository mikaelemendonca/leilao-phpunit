<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;

    public function __construct(string $descricao)
    {
        $this->descricao = $descricao;
        $this->lances = [];
    }

    private function ehDoUltimoUsuario(Lance $lance): bool
    {
        $ultimoLance = end($this->lances);
        return $lance->getUsuario() == $ultimoLance->getUsuario();
    }

    private function quantidadeLancesPorUsuario(Usuario $usuario): int
    {
        return array_reduce(
            $this->lances,
            function (int $totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );
    }

    public function recebeLance(Lance $lance)
    {
        if (
            !empty($this->lances)
            && $this->ehDoUltimoUsuario($lance)                                                                                        
        ) {
            return;
        }

        $totalLances = $this->quantidadeLancesPorUsuario($lance->getUsuario());
        if ($totalLances >= 5) {
            return;
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }
}
