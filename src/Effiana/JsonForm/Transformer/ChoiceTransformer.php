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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ChoiceList\View\ChoiceGroupView;

/**
 * Class ChoiceTransformer
 * @package Effiana\JsonForm\Transformer
 */
class ChoiceTransformer extends AbstractTransformer
{
    /**
     * @param FormInterface $form
     * @param array $extensions
     * @param null $component
     * @return array
     */
    public function transform(FormInterface $form, array $extensions = [], $component = null): array
    {
        $jsonform = $form->getConfig()->getOption('jsonform');
        $isGrouped = false;
        $formView = $form->createView();
        $choices = [];
        $multipleChoices = [];

        foreach ($formView->vars['choices'] as $groupName => $choiceView) {
            if ($choiceView instanceof ChoiceGroupView) {
                foreach ($choiceView->choices as $choiceItem) {
                    if(!array_key_exists($groupName, $multipleChoices)) {
                        $multipleChoices[$groupName] = [
                            'group' => $groupName,
                            'values' => []
                        ];
                    }
                    $multipleChoices[$groupName]['values'][] = [
                        'value' => $this->guessType($choiceItem->value),
                        'label' => $this->translator->trans($choiceItem->label),
                        'selected' => (isset($jsonform['default']) && $this->guessType($jsonform['default']) === $this->guessType($choiceItem->value))
                    ];
                }
            } else {
                $choices[] = [
                    'value' => $this->guessType($choiceView->value),
                    'label' => $this->translator->trans($choiceView->label),
                    'selected' => (isset($jsonform['default']) && $this->guessType($jsonform['default']) === $this->guessType($choiceView->value))
                ];
            }
        }

        if(!empty($multipleChoices)) {
            $isGrouped = true;
            $choices = array_values($multipleChoices);
            $multipleChoices = null;
            unset($multipleChoices);
        }

        $schema = $this->addCommonSpecs($form, [], $extensions, $component);
        $schema['grouped'] = $isGrouped;
        $schema['choices'] = $choices;
        return $schema;
    }

    /**
     * @param $result
     *
     * @return mixed
     */
    private function guessType($result)
    {
        if (is_bool($result) || in_array($result, ['false', 'true'])) {
            return filter_var($result, FILTER_VALIDATE_BOOLEAN);
        }

        if (is_numeric($result)) {
            return filter_var($result, FILTER_VALIDATE_INT);
        }

        return $result;
    }
}
