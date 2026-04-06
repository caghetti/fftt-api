<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Model\Equipe;
use Caghetti\FFTTApi\Model\EquipePoule;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class ListEquipePouleOperation
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
     * @return array<EquipePoule>
     */
    public function listEquipePouleByLienDivision(string $lienDivision): array
    {
        /** @var array<array{clt: string, equipe: string, joue: string, pts: string, numero: string, totvic: string, totdef: string, idequipe: string, idclub: string}> $data */
        $data = $this->client->get('xml_result_equ', ['action' => 'classement'], $lienDivision)['classement'];
        $result = [];
        $lastClassment = 0;

        foreach ($data as $equipePouleData) {
            if (!is_string($equipePouleData['equipe'])) {
                break;
            }

            $result[] = new EquipePoule(
                '-' === $equipePouleData['clt'] ? $lastClassment : (int) $equipePouleData['clt'],
                $equipePouleData['equipe'],
                (int) $equipePouleData['joue'],
                (int) $equipePouleData['pts'],
                $equipePouleData['numero'],
                (int) $equipePouleData['totvic'],
                (int) $equipePouleData['totdef'],
                (int) $equipePouleData['idequipe'],
                $equipePouleData['idclub']
            );
            $lastClassment = '-' == $equipePouleData['clt'] ? $lastClassment : (int) $equipePouleData['clt'];
        }

        return $result;
    }
}
