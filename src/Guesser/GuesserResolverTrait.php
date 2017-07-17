<?php

namespace Joli\Jane\Guesser;

use Joli\Jane\Runtime\Reference;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

trait GuesserResolverTrait
{
    /** @var DenormalizerInterface */
    protected $serializer;

    /**
     * Resolve a reference with a denormalizer
     *
     * @param Reference $reference
     * @param string    $class
     *
     * @return mixed
     */
    public function resolve($reference, $class)
    {
        $result = $reference;

        while ($result instanceof Reference) {
            $result = $result->resolve(function ($data) use($result, $class) {
                return $this->serializer->denormalize($data, $class, 'json', [
                    'document-origin' => (string) $result->getMergedUri()->withFragment('')
                ]);
            });
        }

        return $result;
    }
}
