<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test
{
    /**
     * @var \DateTime
     */
    protected $date;

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate(\DateTime $date = null)
    {
        $this->date = $date;

        return $this;
    }
}
