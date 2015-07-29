<?php

namespace Joli\Jane\Generator;

use PhpParser\Node;

class File
{
    /**
     * @var string
     */
    private $filename;

    /**
     * @var Node
     */
    private $node;

    public function __construct($filename, Node $node)
    {
        $this->filename = $filename;
        $this->node     = $node;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @return Node
     */
    public function getNode()
    {
        return $this->node;
    }
} 
