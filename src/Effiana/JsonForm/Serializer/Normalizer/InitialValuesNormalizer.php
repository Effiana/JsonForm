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

namespace Effiana\JsonForm\Serializer\Normalizer;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Effiana\JsonForm\FormUtil;

/**
 * Normalize instances of FormView
 *
 * @author Nacho Martín <nacho@limenius.com>
 */
class InitialValuesNormalizer implements NormalizerInterface
{
    /**
     * {@inheritdoc}
     */
    public function normalize($form, string $format = null, array $context = [])
    {
        $formView = $form->createView();

        return $this->getValues($form, $formView);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null): bool
    {
        return $data instanceof Form;
    }

    /**
     * @param Form $form
     * @param FormView $formView
     * @return array|mixed|object|null
     */
    private function getValues(Form $form, FormView $formView)
    {
        if (!empty($formView->children)) {
            if ($formView->vars['expanded'] && in_array('choice', FormUtil::typeAncestry($form), true)) {
                if ($formView->vars['multiple']) {
                    return $this->normalizeMultipleExpandedChoice($formView);
                }

                return $this->normalizeExpandedChoice($formView);
            }
            // Force serialization as {} instead of []
            $data = (object) array();
            foreach ($formView->children as $name => $child) {
                // Avoid unknown field error when csrf_protection is true
                // CSRF token should be extracted another way
                if ($form->has($name)) {
                    $data->{$name} = $this->getValues($form->get($name), $child);
                }
            }

            return $data;
        }

        // handle separatedly the case with checkboxes, so the result is
        // true/false instead of 1/0
        return $formView->vars['checked'] ?? $formView->vars['value'];
    }

    /**
     * @param $formView
     * @return array
     */
    private function normalizeMultipleExpandedChoice($formView): array
    {
        $data = [];
        foreach ($formView->children as $name => $child) {
            if ($child->vars['checked']) {
                $data[] = $child->vars['value'];
            }
        }

        return $data;
    }

    /**
     * @param $formView
     * @return |null
     */
    private function normalizeExpandedChoice($formView)
    {
        foreach ($formView->children as $name => $child) {
            if ($child->vars['checked']) {
                return $child->vars['value'];
            }
        }

        return null;
    }
}
