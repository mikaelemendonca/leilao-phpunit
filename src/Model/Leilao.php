<?php

namespace Alura\Leilao\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;
    /** @var string */
    private $descricao;
    /** @var bool */
    private $finalizado;
    /** @var \DateTimeInterface  */
    private $dataInicio;
    /** @var int */
    private $id;

    public function __construct(string $descricao, \DateTimeImmutable $dataInicio = null, int $id = null)
    {
        $this->descricao = $descricao;
        $this->finalizado = false;
        $this->lances = [];
        $this->dataInicio = $dataInicio ?? new \DateTimeImmutable();
        $this->id = $id;
    }

    public function finaliza()
    {
        $this->finalizado = true;
    }

    public function getFinalizado()
    {
        return $this->finalizado;
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
            throw new \DomainException('Usuário não pode propor 2 lances consecutivos');
        }

        $totalLances = $this->quantidadeLancesPorUsuario($lance->getUsuario());
        if ($totalLances >= 5) {
            throw new \DomainException('Usuário não pode propor mais de 5 lances por leilão');
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

    public function recuperarDescricao(): string
    {
        return $this->descricao;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }

    public function recuperarDataInicio(): \DateTimeInterface
    {
        return $this->dataInicio;
    }

    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTime();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    public function recuperarId(): int
    {
        return $this->id;
    }
}
