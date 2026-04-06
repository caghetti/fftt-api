<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Exception\InvalidLienRencontreException;
use Caghetti\FFTTApi\Model\Factory\RencontreDetailsFactory;
use Caghetti\FFTTApi\Model\Rencontre\RencontreDetails;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class RetrieveRencontreDetailsOperation
{
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\FFTTClientInterface
     */
    private $client;
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Model\Factory\RencontreDetailsFactory
     */
    private $rencontreDetailsFactory;
    public function __construct(FFTTClientInterface $client, RencontreDetailsFactory $rencontreDetailsFactory)
    {
        $this->client = $client;
        $this->rencontreDetailsFactory = $rencontreDetailsFactory;
    }
    public function retrieveRencontreDetailsByLien(string $lienRencontre, string $clubEquipeA = '', string $clubEquipeB = ''): RencontreDetails
    {
        $data = $this->client->get('xml_chp_renc', [], $lienRencontre);
        if (!(isset($data['resultat']) && isset($data['joueur']) && isset($data['partie']))) {
            throw new InvalidLienRencontreException($lienRencontre);
        }

        return $this->rencontreDetailsFactory->createFromArray($data, $clubEquipeA, $clubEquipeB);
    }
}
