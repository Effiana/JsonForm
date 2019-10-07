<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Effiana\JsonForm\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Adds a 'jsonform' configuration option to instances of FormType
 *
 * @author Nacho Martín <nacho@limenius.com>
 */
class AddJsonFormExtension extends AbstractTypeExtension
{
    /**
     * Returns the name of the type being extended.
     *
     * @return array
     */
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    /**
     * Add the jsonform option
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefined(['jsonform']);
    }
}
