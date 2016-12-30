<?php

declare(strict_types=1);

namespace Joli\Jane\AstGenerator\Writer;

use PhpParser\Node\Stmt;
use PhpParser\PrettyPrinterAbstract;

/**
 * Writer
 */
class NamespaceWriter implements WriterInterface
{
    private $namespaces = [];

    private $prettyPrinter;

    public function __construct(PrettyPrinterAbstract $prettyPrinter)
    {
        $this->prettyPrinter = $prettyPrinter;
    }

    public function registerNamespace($namespace, $directory)
    {
        $this->namespaces[$namespace] = $directory;
    }

    /**
     * {@inheritdoc}
     */
    public function write($nodes)
    {
        $files = [];

        /** @var Stmt\Namespace_ $node */
        foreach ($nodes as $node) {
            if (!$node instanceof Stmt\Namespace_) {
                continue;
            }

            foreach ($node->stmts as $subNode) {
                if (!$subNode instanceof Stmt\Class_) {
                    continue;
                }

                $file = $this->getFile($node->name->toString(), $subNode->name);
                $files[] = $file;

                file_put_contents($file, $this->prettyPrinter->prettyPrintFile([
                    new Stmt\Namespace_($node->name, [
                        $subNode
                    ])
                ]));
            }
        }

        return $files;
    }

    /**
     * @param $namespace
     * @param $class
     *
     * @return string
     */
    private function getFile($namespace, $class)
    {
        $foundNamespace = null;
        $foundNamespaceLeft = null;

        foreach ($this->namespaces as $registeredNamespace => $registeredDirectory) {
            if (preg_match('/' . preg_quote($registeredNamespace, '/') . '/', $namespace)) {
                $namespaceLeft = count(explode('\\', str_replace($registeredNamespace, '', $namespace)));

                if ($foundNamespace === null || $namespaceLeft < $foundNamespaceLeft) {
                    $foundNamespace = $registeredNamespace;
                    $foundNamespaceLeft = $namespaceLeft;
                }
            }
        }

        if ($foundNamespace === null) {
            throw new \RuntimeException();
        }

        $directory = $this->namespaces[$foundNamespace];
        $directory .= implode(DIRECTORY_SEPARATOR, explode('\\', str_replace($foundNamespace, '', $namespace)));

        return $directory . DIRECTORY_SEPARATOR . $class . '.php';
    }
}
