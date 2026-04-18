<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Model;

final class UnvalidatedPartie
{
    /**
     * @readonly
     * @var string
     */
    private $epreuve;
    /**
     * @readonly
     * @var string
     */
    private $idPartie;
    /**
     * @readonly
     * @var float
     */
    private $coefficientChampionnat;
    /**
     * @readonly
     * @var bool
     */
    private $isVictoire;
    /**
     * @readonly
     * @var bool
     */
    private $isForfait;
    /**
     * @readonly
     * @var \DateTime
     */
    private $date;
    /**
     * @readonly
     * @var string
     */
    private $adversaireNom;
    /**
     * @readonly
     * @var string
     */
    private $adversairePrenom;
    /**
     * @readonly
     * @var int
     */
    private $adversaireClassement;
    /**
     * Points issus du champ pointres de l'API (xml_partie), avant application du coefficient championnat.
     *
     * @readonly
     * @var float
     */
    private $pointsObtenus;
    public function __construct(string $epreuve, string $idPartie, float $coefficientChampionnat, bool $isVictoire, bool $isForfait, \DateTime $date, string $adversaireNom, string $adversairePrenom, int $adversaireClassement, float $pointsObtenus = 0.0)
    {
        $this->epreuve = $epreuve;
        $this->idPartie = $idPartie;
        $this->coefficientChampionnat = $coefficientChampionnat;
        $this->isVictoire = $isVictoire;
        $this->isForfait = $isForfait;
        $this->date = $date;
        $this->adversaireNom = $adversaireNom;
        $this->adversairePrenom = $adversairePrenom;
        $this->adversaireClassement = $adversaireClassement;
        $this->pointsObtenus = $pointsObtenus;
    }
    public function getEpreuve(): string
    {
        return $this->epreuve;
    }

    public function getIdPartie(): string
    {
        return $this->idPartie;
    }

    public function getCoefficientChampionnat(): float
    {
        return $this->coefficientChampionnat;
    }

    public function isVictoire(): bool
    {
        return $this->isVictoire;
    }

    public function isForfait(): bool
    {
        return $this->isForfait;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function getAdversaireNom(): string
    {
        return $this->adversaireNom;
    }

    public function getAdversairePrenom(): string
    {
        return $this->adversairePrenom;
    }

    public function getAdversaireClassement(): int
    {
        return $this->adversaireClassement;
    }

    public function getPointsObtenus(): float
    {
        return $this->pointsObtenus;
    }
}
