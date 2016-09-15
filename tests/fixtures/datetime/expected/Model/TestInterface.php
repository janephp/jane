<?php

namespace Joli\Jane\Tests\Expected\Model;

interface TestInterface
{
    /**
     * @return \DateTime
     */
    public function getDate();

    /**
     * @param \DateTime $date
     *
     * @return self
     */
    public function setDate(\DateTime $date = null);

    /**
     * @return \DateTime|null
     */
    public function getDateOrNull();

    /**
     * @param \DateTime|null $dateOrNull
     *
     * @return self
     */
    public function setDateOrNull(\DateTime $dateOrNull = null);

    /**
     * @return \DateTime|null|int
     */
    public function getDateOrNullOrInt();

    /**
     * @param \DateTime|null|int $dateOrNullOrInt
     *
     * @return self
     */
    public function setDateOrNullOrInt($dateOrNullOrInt = null);
}
