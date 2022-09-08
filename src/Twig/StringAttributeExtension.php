<?php

declare(strict_types=1);

namespace App\Twig;

use RuntimeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Has the same ability as twig own 'attribute' function but instead of taking an object as first parameter take a string
 * as object name and looks for it in twig's context array.
 */
final class StringAttributeExtension extends AbstractExtension
{
    private PropertyAccessor $propertyAccessor;

    public function __construct()
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('stringAttribute', [$this, 'computeStringAttribute'], ['needs_context' => true]),
        ];
    }

    public function computeStringAttribute(array $context, string $expr)
    {
        [$objectName, $propertyName] = explode('.', $expr);

        $object = $context[$objectName] ?? null;

        if(!$object) {
            throw new RuntimeException(sprintf('Object "%s" not found in twig\' context.', $objectName));
        }

        return $this->propertyAccessor->getValue($object, $propertyName);
    }
}
