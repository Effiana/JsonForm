<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Effiana\JsonForm\Transformer;

use Effiana\JsonForm\Exception\TransformerException;
use Effiana\JsonForm\ResolverInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class ArrayTransformer extends AbstractTransformer
{
    /**
     * @var ResolverInterface
     */
    protected $resolver;

    /**
     * @param TranslatorInterface           $translator
     * @param FormTypeGuesserInterface|null $validatorGuesser
     * @param ResolverInterface             $resolver
     */
    public function __construct(
        TranslatorInterface $translator,
        ?FormTypeGuesserInterface $validatorGuesser,
        ResolverInterface $resolver
    ) {
        parent::__construct($translator, $validatorGuesser);
        $this->resolver = $resolver;
    }

    /**
     * @param FormInterface $form
     * @param array $extensions
     * @param null $component
     * @return array
     */
    public function transform(FormInterface $form, array $extensions = [], $component = null): array
    {
        $children = [];

        foreach ($form->all() as $name => $field) {
            $transformerData = $this->resolver->resolve($field);
            $transformedChild = $transformerData['transformer']->transform($field, $extensions, $transformerData['component']);
            $children[] = $transformedChild;

            if ($transformerData['transformer']->isRequired($field)) {
                $required[] = $field->getName();
            }
        }

        if (empty($children)) {
            $entryType = $form->getConfig()->getAttribute('prototype');

            if (!$entryType) {
                throw new TransformerException('JsonForm cannot infer the json-schema representation of a an empty Collection or array-like type without the option "allow_add" (to check the proptotype). Evaluating "'.$form->getName().'"');
            }

            $transformerData = $this->resolver->resolve($entryType);
            $children[] = $transformerData['transformer']->transform($entryType, $extensions, $transformerData['component']);
            $children[0]['label'] = 'prototype';
        }

        $schema = [
            'label' => $form->getConfig()->getOption('label'),
            'items' => $children[0],
        ];

        $schema = $this->addCommonSpecs($form, $schema, $extensions, $component);

        return $schema;
    }
}
