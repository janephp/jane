<?php

namespace Joli\Jane\Tests\Expected\Model;

interface TestInterface
{
    /**
     * @return Childtype
     */
    public function getChild();

    /**
     * @param Childtype $child
     *
     * @return self
     */
    public function setChild(ChildtypeInterface $child = null);

    /**
     * @return Parenttype
     */
    public function getParent();

    /**
     * @param Parenttype $parent
     *
     * @return self
     */
    public function setParent(ParenttypeInterface $parent = null);
}
