<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Model\Equipe;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class ListEquipeOperation
{
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\FFTTClientInterface
     */
    private $client;
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\Operation\ArrayWrapper
     */
    private $arrayWrapper;
    public function __construct(FFTTClientInterface $client, ArrayWrapper $arrayWrapper)
    {
        $this->client = $client;
        $this->arrayWrapper = $arrayWrapper;
    }
    /**
     * @return array<Equipe>
     */
    public function listEquipesByClub(string $clubId, ?string $type = null): array
    {
        $params = [
            'numclu' => $clubId,
        ];

        if ($type) {
            $params['type'] = $type;
        }

        /** @var array<mixed>|null $data */
        $data = $this->client->get('xml_equipe', $params
        )['equipe'] ?? null;

        if (null === $data) {
            return [];
        }

        $data = $this->arrayWrapper->wrapArrayIfUnique($data);

        $result = [];
        /** @var array{libequipe: string, libdivision: string, liendivision: string} $dataEquipe */
        foreach ($data as $dataEquipe) {
            $result[] = new Equipe(
                $dataEquipe['libequipe'],
                $dataEquipe['libdivision'],
                $dataEquipe['liendivision']
            );
        }

        return $result;
    }
}
