<?php

declare(strict_types = 1);

namespace Joli\Jane\AstGenerator\Generator\Hydrate;

use Joli\Jane\AstGenerator\Generator\AstGeneratorInterface;
use Joli\Jane\AstGenerator\Generator\Exception\MissingContextException;
use Joli\Jane\AstGenerator\Generator\Exception\NotSupportedGeneratorException;
use Joli\Jane\AstGenerator\UniqueVariableScope;
use PhpParser\Node\Arg;
use PhpParser\Node\Name;
use PhpParser\Node\Expr;
use Symfony\Component\PropertyInfo\PropertyInfoExtractorInterface;

abstract class Object implements AstGeneratorInterface
{
    /** @var PropertyInfoExtractorInterface Extract list of properties from a class */
    protected $propertyInfoExtractor;

    /** @var AstGeneratorInterface AstGenerator for hydration of types */
    protected $typeHydrateAstGenerator;

    public function __construct(PropertyInfoExtractorInterface $propertyInfoExtractor, AstGeneratorInterface $typeHydrateAstGenerator)
    {
        $this->propertyInfoExtractor = $propertyInfoExtractor;
        $this->typeHydrateAstGenerator = $typeHydrateAstGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate($object, array $context = array())
    {
        if (!is_string($object) || count($this->propertyInfoExtractor->getProperties($object)) === 0) {
            throw new NotSupportedGeneratorException();
        }

        if (!isset($context['input']) || !($context['input'] instanceof Expr\Variable)) {
            throw new MissingContextException('Input variable not defined or not a Expr\Variable in generation context');
        }

        if (!isset($context['output']) || !($context['output'] instanceof Expr\Variable)) {
            throw new MissingContextException('Output variable not defined or not a Expr\Variable in generation context');
        }

        $uniqueVariableScope = isset($context['unique_variable_scope']) ? $context['unique_variable_scope'] : new UniqueVariableScope();
        $statements = array(
            new Expr\Assign($context['output'], new Expr\New_(new Name("\\".$object))),
        );

        foreach ($this->propertyInfoExtractor->getProperties($object, $context) as $property) {
            // Only hydrate writable property
            if (!$this->propertyInfoExtractor->isWritable($object, $property, $context)) {
                continue;
            }

            $output = new Expr\Variable($uniqueVariableScope->getUniqueName('output'));
            $input = $this->createInputExpr($context['input'], $property);
            $types = $this->propertyInfoExtractor->getTypes($object, $property, $context);

            // If no type can be extracted, directly assign output to input
            if (null === $types || count($types) === 0) {
                // @TODO Have property info extractor extract the way of writing a property (public or method with method name)
                $statements[] = new Expr\MethodCall($context['output'], 'set'.ucfirst($property), [
                    new Arg($input)
                ]);

                continue;
            }

            // If there is multiple types, we need to know which one we must normalize
            $conditionNeeded = (boolean) (count($types) > 1);
            $noAssignment = true;

            foreach ($types as $type) {
                if (!$this->typeHydrateAstGenerator->supportsGeneration($type)) {
                    continue;
                }

                $noAssignment = false;
                $statements = array_merge($statements, $this->typeHydrateAstGenerator->generate($type, array_merge($context, [
                    'input' => $input,
                    'output' => $output,
                    'condition' => $conditionNeeded,
                ])));
            }

            // If nothing has been assigned, we directly put input into output
            if ($noAssignment) {
                // @TODO Have property info extractor extract the way of writing a property (public or method with method name)
                $statements[] = new Expr\MethodCall($context['output'], 'set'.ucfirst($property), [
                    new Arg($input)
                ]);
            } else {
                $statements[] = new Expr\MethodCall($context['output'], 'set'.ucfirst($property), [
                    new Arg($output)
                ]);
            }
        }

        return $statements;
    }

    /**
     * Create the input expression for a specific property
     *
     * @param Expr\Variable $inputVariable Input variable of data
     * @param string        $property      Property to fetch
     *
     * @return Expr
     */
    abstract protected function createInputExpr(Expr\Variable $inputVariable, $property);
}
