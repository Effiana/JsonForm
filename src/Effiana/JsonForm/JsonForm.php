<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Effiana\JsonForm;

use Effiana\JsonForm\Transformer\ExtensionInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class JsonForm implements JsonFormInterface
{
    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var ExtensionInterface[]
     */
    private $extensions = [];

    /**
     * @param ResolverInterface $resolver
     */
    public function __construct(ResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form): array
    {
        $transformerData = $this->resolver->resolve($form);
        return $transformerData['transformer']->transform($form, $this->extensions, $transformerData['component']);
    }

    /**
     * {@inheritdoc}
     */
    public function addExtension(ExtensionInterface $extension): JsonFormInterface
    {
        $this->extensions[] = $extension;

        return $this;
    }
}
