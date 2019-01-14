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

use Effiana\JsonForm\Transformer\TransformerInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
interface ResolverInterface
{
    /**
     * @param string               $formType
     * @param TransformerInterface $transformer
     * @param string|null          $widget
     */
    public function setTransformer($formType, TransformerInterface $transformer, $widget = null);

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public function resolve(FormInterface $form);
}
