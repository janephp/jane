<?php

namespace Joli\Jane\Guesser;

interface ChainGuesserAwareInterface
{
    /**
     * Set the chain guesser
     *
     * @param ChainGuesser $chainGuesser
     *
     * @internal
     */
    public function setChainGuesser(ChainGuesser $chainGuesser);
}
