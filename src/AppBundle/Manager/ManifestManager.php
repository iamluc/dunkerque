<?php

namespace AppBundle\Manager;

use AppBundle\Entity\Manifest;
use Doctrine\Common\Persistence\ObjectManager;

class ManifestManager
{
    /**
     * @var ObjectManager
     */
    private $om;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function save(Manifest $manifest)
    {
        $this->om->persist($manifest);
        $this->om->flush();
    }

    public function create($raw)
    {
        $digest = $this->computeDigest($raw);
        $decoded = json_decode($raw, true);

        return new Manifest($decoded['name'], $decoded['tag'], $digest, $raw);
    }

    public function get($name, $reference)
    {
        $manifest = $this->om->getRepository('AppBundle:Manifest')->findOneBy([
            'name' => $name,
            'tag' => $reference,
        ]);

        if (null === $manifest) {
            $manifest = $this->om->getRepository('AppBundle:Manifest')->findOneBy([
                'name' => $name,
                'digest' => $reference,
            ]);
        }

        return $manifest;
    }

    /**
     * Return the digest of the provided manifest.
     *
     * @param $manifest
     *
     * @return string
     */
    public function computeDigest($manifest)
    {
        // See func `ParsePrettySignature` in https://github.com/docker/libtrust/blob/master/jsonsign.go
        $decoded = json_decode($manifest, true);
        $formatLength = null;
        $formatTail = null;
        foreach ($decoded['signatures'] as $signature) {
            $header = json_decode(base64_decode($signature['protected']), true);

            // TODO: validate signatures

            $formatLength = $header['formatLength'];
            $formatTail = $header['formatTail'];
        }

        // Manifest without signatures
        $manifest = substr($manifest, 0, $formatLength).base64_decode($formatTail);

        // Digest
        return 'sha256:'.hash('sha256', $manifest);
    }
}
