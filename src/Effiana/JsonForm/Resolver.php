<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Effiana\JsonForm;

use Effiana\JsonForm\Exception\TransformerException;
use Effiana\JsonForm\Transformer\TransformerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class Resolver implements ResolverInterface
{
    /**
     * @var TransformerInterface[]
     */
    private $transformers = [];

    /**
     * @param string               $formType
     * @param TransformerInterface $transformer
     * @param string|null          $component
     */
    public function setTransformer($formType, TransformerInterface $transformer, $component = null)
    {
        $this->transformers[$formType] = [
            'transformer' => $transformer,
            'component' => $component,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(FormInterface $form)
    {
        $types = FormUtil::typeAncestry($form);

        foreach ($types as $type) {
            if (isset($this->transformers[$type])) {
                return $this->transformers[$type];
            }
        }

        // Perhaps a compound we don't have a specific transformer for
        if (FormUtil::isCompound($form)) {
            return [
                'transformer' => $this->transformers['compound']['transformer'],
                'component' => null,
            ];
        }

        throw new TransformerException(
            sprintf(
                'Could not find a transformer for any of these types (%s)',
                implode(', ', $types)
            )
        );
    }
}
