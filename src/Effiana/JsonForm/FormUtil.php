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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormTypeInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class FormUtil
{
    /**
     * @param FormInterface $form
     *
     * @return array
     */
    public static function typeAncestry(FormInterface $form): array
    {
        $types = [];
        self::typeAncestryForType($form->getConfig()->getType(), $types);

        return $types;
    }

    /**
     * @param ResolvedFormTypeInterface $formType
     * @param array                     $types
     *
     * @return void
     */
    public static function typeAncestryForType(?ResolvedFormTypeInterface $formType, array &$types): void
    {
        if (!($formType instanceof ResolvedFormTypeInterface)) {
            return;
        }

        $types[] = $formType->getBlockPrefix();

        self::typeAncestryForType($formType->getParent(), $types);
    }

    /**
     * Returns the dataClass of the form or its parents, if any
     *
     * @param mixed $formType
     *
     * @return string|null the dataClass
     */
    public static function findDataClass($formType): ?string
    {
        if ($dataClass = $formType->getConfig()->getDataClass()) {
            return $dataClass;
        }

        if ($parent = $formType->getParent()) {
            return self::findDataClass($parent);
        }

        return null;
    }

    /**
     * @param FormInterface $form
     * @param mixed         $type
     *
     * @return boolean
     */
    public static function isTypeInAncestry(FormInterface $form, $type)
    {
        return in_array($type, self::typeAncestry($form), true);
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    public static function type(FormInterface $form): string
    {
        return $form->getConfig()->getType()->getName();
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    public static function label(FormInterface $form): string
    {
        return $form->getConfig()->getOption('label', $form->getName());
    }

    /**
     * @param FormInterface $form
     *
     * @return string
     */
    public static function isCompound(FormInterface $form): string
    {
        return $form->getConfig()->getOption('compound');
    }
}
