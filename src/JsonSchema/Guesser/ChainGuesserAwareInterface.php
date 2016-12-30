<?php

namespace Joli\Jane\JsonSchema\Guesser;

interface ChainGuesserAwareInterface
{
    /**
     * Set the chain guesser
     *
     * @param ChainGuesser $chainGuesser
     */
    public function setChainGuesser(ChainGuesser $chainGuesser);
}
