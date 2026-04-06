<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Model\Enums\TypeEpreuveEnum;
use Caghetti\FFTTApi\Model\Epreuve;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class ListEpreuveOperation
{
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\FFTTClientInterface
     */
    private $client;
    public function __construct(FFTTClientInterface $client)
    {
        $this->client = $client;
    }
    /**
     * @return array<Epreuve>
     */
    public function listEpreuves(int $organisme, TypeEpreuveEnum $type): array
    {
        /** @var array<array{idepreuve: string, idorga: string, libelle:string, typepreuve: string}> $epreuves */
        $epreuves = $this->client->get('xml_epreuve', [
            'type' => $type->value,
            'organisme' => (string) $organisme,
        ])['epreuve'];

        $result = [];
        foreach ($epreuves as $epreuve) {
            $result[] = new Epreuve(
                (int) $epreuve['idepreuve'],
                (int) $epreuve['idorga'],
                $epreuve['libelle'],
                $epreuve['typepreuve']
            );
        }

        return $result;
    }
}
