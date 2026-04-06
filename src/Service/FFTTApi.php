<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service;

use Caghetti\FFTTApi\Model\Actualite;
use Caghetti\FFTTApi\Model\Classement;
use Caghetti\FFTTApi\Model\Club;
use Caghetti\FFTTApi\Model\ClubDetails;
use Caghetti\FFTTApi\Model\Enums\TypeEpreuveEnum;
use Caghetti\FFTTApi\Model\Epreuve;
use Caghetti\FFTTApi\Model\Equipe;
use Caghetti\FFTTApi\Model\EquipePoule;
use Caghetti\FFTTApi\Model\Factory\ClubFactory;
use Caghetti\FFTTApi\Model\Factory\RencontreDetailsFactory;
use Caghetti\FFTTApi\Model\Historique;
use Caghetti\FFTTApi\Model\Joueur;
use Caghetti\FFTTApi\Model\JoueurDetails;
use Caghetti\FFTTApi\Model\Organisme;
use Caghetti\FFTTApi\Model\Partie;
use Caghetti\FFTTApi\Model\Rencontre\Rencontre;
use Caghetti\FFTTApi\Model\Rencontre\RencontreDetails;
use Caghetti\FFTTApi\Model\UnvalidatedPartie;
use Caghetti\FFTTApi\Model\VirtualPoints;
use Caghetti\FFTTApi\Service\Operation\ArrayWrapper;
use Caghetti\FFTTApi\Service\Operation\ListActualiteOperation;
use Caghetti\FFTTApi\Service\Operation\ListClubOperation;
use Caghetti\FFTTApi\Service\Operation\ListEpreuveOperation;
use Caghetti\FFTTApi\Service\Operation\ListEquipeOperation;
use Caghetti\FFTTApi\Service\Operation\ListEquipePouleOperation;
use Caghetti\FFTTApi\Service\Operation\ListHistoriqueOperation;
use Caghetti\FFTTApi\Service\Operation\ListJoueurOperation;
use Caghetti\FFTTApi\Service\Operation\ListOrganismeOperation;
use Caghetti\FFTTApi\Service\Operation\ListPartieOperation;
use Caghetti\FFTTApi\Service\Operation\ListRencontreOperation;
use Caghetti\FFTTApi\Service\Operation\RetrieveClassementOperation;
use Caghetti\FFTTApi\Service\Operation\RetrieveClubDetailsOperation;
use Caghetti\FFTTApi\Service\Operation\RetrieveJoueurDetailsOperation;
use Caghetti\FFTTApi\Service\Operation\RetrieveRencontreDetailsOperation;
use Caghetti\FFTTApi\Service\Operation\RetrieveVirtualPointsOperation;
use GuzzleHttp\Client;

/**
 * This class is not memory efficient but is easy to use
 * If you have a dependency injection system, inject operations instead.
 */
final class FFTTApi
{
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListOrganismeOperation
     */
    private $listOrganismeOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListClubOperation
     */
    private $listClubOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\RetrieveClubDetailsOperation
     */
    private $retrieveClubDetailsOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListJoueurOperation
     */
    private $listJoueurOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\RetrieveJoueurDetailsOperation
     */
    private $retrieveJoueurDetailsOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\RetrieveClassementOperation
     */
    private $retrieveClassementOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListHistoriqueOperation
     */
    private $listHistoriqueOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListPartieOperation
     */
    private $listPartieOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\RetrieveVirtualPointsOperation
     */
    private $virtualPointsOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListEquipeOperation
     */
    private $listEquipeOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListEquipePouleOperation
     */
    private $listEquipePouleOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListRencontreOperation
     */
    private $listRencontreOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\RetrieveRencontreDetailsOperation
     */
    private $retrieveRencontreDetailsOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListActualiteOperation
     */
    private $listActualiteOperation;
    /**
     * @var \Caghetti\FFTTApi\Service\Operation\ListEpreuveOperation
     */
    private $listEpreuveOperation;

    public function __construct(string $id, string $password)
    {
        $arrayWrapper = new ArrayWrapper();

        $uriGenerator = new UriGenerator($id, $password);
        $FFTTClient = new FFTTClient(new Client(), $uriGenerator);

        $this->listOrganismeOperation = new ListOrganismeOperation($FFTTClient);

        $clubFactory = new ClubFactory();
        $this->listClubOperation = new ListClubOperation($FFTTClient, $clubFactory, $arrayWrapper);

        $this->retrieveClubDetailsOperation = new RetrieveClubDetailsOperation($FFTTClient, $this->listClubOperation);
        $this->listJoueurOperation = new ListJoueurOperation($FFTTClient, $arrayWrapper);
        $this->retrieveJoueurDetailsOperation = new RetrieveJoueurDetailsOperation($FFTTClient);
        $this->retrieveClassementOperation = new RetrieveClassementOperation($FFTTClient);
        $this->listHistoriqueOperation = new ListHistoriqueOperation($FFTTClient, $arrayWrapper);

        $nomPrenomExtractor = new NomPrenomExtractor();
        $this->listPartieOperation = new ListPartieOperation($FFTTClient, $arrayWrapper, $nomPrenomExtractor);

        $pointCalculator = new PointCalculator();
        $this->virtualPointsOperation = new RetrieveVirtualPointsOperation($this->retrieveClassementOperation, $this->listPartieOperation, $this->listJoueurOperation, $pointCalculator);

        $this->listEquipeOperation = new ListEquipeOperation($FFTTClient, $arrayWrapper);
        $this->listEquipePouleOperation = new ListEquipePouleOperation($FFTTClient);
        $this->listRencontreOperation = new ListRencontreOperation($FFTTClient);

        $rencontreDetailsFactory = new RencontreDetailsFactory($nomPrenomExtractor, $this->listJoueurOperation);
        $this->retrieveRencontreDetailsOperation = new RetrieveRencontreDetailsOperation($FFTTClient, $rencontreDetailsFactory);
        $this->listActualiteOperation = new ListActualiteOperation($FFTTClient, $arrayWrapper);
        $this->listEpreuveOperation = new ListEpreuveOperation($FFTTClient);
    }

    /**
     * @return array<Organisme>
     */
    public function listOrganismes(string $type = 'Z'): array
    {
        return $this->listOrganismeOperation->listOrganismes($type);
    }

    /**
     * @return array<Club>
     */
    public function listClubsByDepartement(int $departementId): array
    {
        return $this->listClubOperation->listClubsByDepartement($departementId);
    }

    /**
     * @return array<Club>
     */
    public function listClubsByName(string $name): array
    {
        return $this->listClubOperation->listClubsByName($name);
    }

    public function retrieveClubDetails(string $clubId): ClubDetails
    {
        return $this->retrieveClubDetailsOperation->retrieveClubDetails($clubId);
    }

    /**
     * @return array<Joueur>
     */
    public function listJoueursByClub(string $clubId): array
    {
        return $this->listJoueurOperation->listJoueursByClub($clubId);
    }

    /**
     * @return array<Joueur>
     */
    public function listJoueursByNom(string $nom, string $prenom = ''): array
    {
        return $this->listJoueurOperation->listJoueursByNom($nom, $prenom);
    }

    /**
     * @return JoueurDetails|array<JoueurDetails>
     */
    public function retrieveJoueurDetails(string $licenceId, ?string $clubId = null)
    {
        return $this->retrieveJoueurDetailsOperation->retrieveJoueurDetails($licenceId, $clubId);
    }

    public function retrieveClassement(string $licenceId): Classement
    {
        return $this->retrieveClassementOperation->retrieveClassement($licenceId);
    }

    /**
     * @return array<Historique>
     */
    public function listHistorique(string $licenceId): array
    {
        return $this->listHistoriqueOperation->listHistorique($licenceId);
    }

    /**
     * @return array<Partie>
     */
    public function listPartiesJoueurByLicence(string $licenceId): array
    {
        return $this->listPartieOperation->listPartiesJoueurByLicence($licenceId);
    }

    /**
     * @return array<UnvalidatedPartie>
     */
    public function listUnvalidatedPartiesJoueurByLicence(string $licenceId): array
    {
        return $this->listPartieOperation->listUnvalidatedPartiesJoueurByLicence($licenceId);
    }

    public function retrieveVirtualPoints(string $licenceId): VirtualPoints
    {
        return $this->virtualPointsOperation->retrieveVirtualPoints($licenceId);
    }

    /**
     * @return array<Equipe>
     */
    public function listEquipesByClub(string $clubId, ?string $type = null): array
    {
        return $this->listEquipeOperation->listEquipesByClub($clubId, $type);
    }

    /**
     * @return array<EquipePoule>
     */
    public function listEquipePouleByLienDivision(string $lienDivision): array
    {
        return $this->listEquipePouleOperation->listEquipePouleByLienDivision($lienDivision);
    }

    /**
     * @return array<Rencontre>
     */
    public function listRencontrePouleByLienDivision(string $lienDivision): array
    {
        return $this->listRencontreOperation->listRencontrePouleByLienDivision($lienDivision);
    }

    /**
     * @return array<Rencontre>
     */
    public function listProchainesRencontresEquipe(Equipe $equipe): array
    {
        return $this->listRencontreOperation->listProchainesRencontresEquipe($equipe);
    }

    public function retrieveClubDetailsByEquipe(Equipe $equipe): ?ClubDetails
    {
        return $this->retrieveClubDetailsOperation->retrieveClubDetailsByEquipe($equipe);
    }

    public function retrieveRencontreDetailsByLien(string $lienRencontre, string $clubEquipeA = '', string $clubEquipeB = ''): RencontreDetails
    {
        return $this->retrieveRencontreDetailsOperation->retrieveRencontreDetailsByLien($lienRencontre, $clubEquipeA, $clubEquipeB);
    }

    /**
     * @return array<Actualite>
     */
    public function listActualites(): array
    {
        return $this->listActualiteOperation->listActualites();
    }

    /**
     * @return array<Epreuve>
     */
    public function listEpreuves(int $organisme, TypeEpreuveEnum $type = TypeEpreuveEnum::Equipe): array
    {
        return $this->listEpreuveOperation->listEpreuves($organisme, $type);
    }
}
