<?php declare(strict_types=1);

namespace Caghetti\FFTTApi\Service\Operation;

use Caghetti\FFTTApi\Model\Actualite;
use Caghetti\FFTTApi\Service\FFTTClientInterface;

final class ListActualiteOperation
{
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\FFTTClientInterface
     */
    private $FFTTClient;
    /**
     * @readonly
     * @var \Caghetti\FFTTApi\Service\Operation\ArrayWrapper
     */
    private $arrayWrapper;
    public function __construct(FFTTClientInterface $FFTTClient, ArrayWrapper $arrayWrapper)
    {
        $this->FFTTClient = $FFTTClient;
        $this->arrayWrapper = $arrayWrapper;
    }
    /**
     * @return array<Actualite>
     */
    public function listActualites(): array
    {
        /** @var array<mixed> $data */
        $data = $this->FFTTClient->get('xml_new_actu')['news'];
        $data = $this->arrayWrapper->wrapArrayIfUnique($data);

        $result = [];
        /** @var array{date: string, titre: string, description: string, url: string, photo: string, categorie: string} $dataActualite */
        foreach ($data as $dataActualite) {
            /** @var \DateTime $date */
            $date = \DateTime::createFromFormat('!Y-m-d', $dataActualite['date']);
            $result[] = new Actualite(
                $date,
                $dataActualite['titre'],
                $dataActualite['description'],
                $dataActualite['url'],
                $dataActualite['photo'],
                $dataActualite['categorie']
            );
        }

        return $result;
    }
}
