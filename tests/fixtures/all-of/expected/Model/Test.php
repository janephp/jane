<?php

namespace Joli\Jane\Tests\Expected\Model;

class Test implements TestInterface
{
    /**
     * @var Childtype
     */
    protected $child;
    /**
     * @var Parenttype
     */
    protected $parent;

    /**
     * @return Childtype
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * @param Childtype $child
     *
     * @return self
     */
    public function setChild(ChildtypeInterface $child = null)
    {
        $this->child = $child;

        return $this;
    }

    /**
     * @return Parenttype
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Parenttype $parent
     *
     * @return self
     */
    public function setParent(ParenttypeInterface $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }
}
