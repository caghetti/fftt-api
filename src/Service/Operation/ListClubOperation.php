<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Model\Club;
use Caghetti\FFTTApi\Model\Factory\ClubFactory;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class ListClubOperation
{
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\FFTTClientInterface
     */
    private $client;
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Model\Factory\ClubFactory
     */
    private $clubFactory;
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\Operation\ArrayWrapper
     */
    private $arrayWrapper;
    public function __construct(FFTTClientInterface $client, ClubFactory $clubFactory, ArrayWrapper $arrayWrapper)
    {
        $this->client = $client;
        $this->clubFactory = $clubFactory;
        $this->arrayWrapper = $arrayWrapper;
    }
    /**
     * @return array<Club>
     */
    public function listClubsByDepartement(int $departementId): array
    {
        /** @var array<array{numero: string, nom: string, validation: array<mixed>|string}> $rawClubs */
        $rawClubs = $this->client->get('xml_club_dep2', [
            'dep' => str_pad((string) $departementId, 2, '0', STR_PAD_LEFT),
        ])['club'] ?? [];

        return $this->clubFactory->createFromArray($rawClubs);
    }

    /**
     * @return array<Club>
     */
    public function listClubsByName(string $name): array
    {
        try {
            /** @var array<mixed> $rawClubs */
            $rawClubs = $this->client->get('xml_club_b', [
                'ville' => $name,
            ])['club'] ?? [];

            /** @var array<array{numero: string, nom: string, validation: array<mixed>|string}> $rawClubs */
            $rawClubs = $this->arrayWrapper->wrapArrayIfUnique($rawClubs);

            return $this->clubFactory->createFromArray($rawClubs);
        } catch (\Exception $e) {
            return [];
        }
    }
}
