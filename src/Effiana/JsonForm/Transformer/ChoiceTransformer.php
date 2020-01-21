<?php
/**
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Effiana\JsonForm\Transformer {

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
            $multiple = (bool)$form->getConfig()->getOption('multiple');
            $translationDomain = $form->getConfig()->getOption('translation_domain');
            $isGrouped = false;
            $formView = $form->createView();
            $choices = [];
            $multipleChoices = [];

            foreach ($formView->vars['choices'] as $groupName => $choiceView) {
                if ($choiceView instanceof ChoiceGroupView) {
                    foreach ($choiceView->choices as $choiceItem) {
                        $isSingle = ($choiceItem->data instanceof ChoiceTransformerInterface) ? $choiceItem->data->isSingle() : !$multiple;
                        if (!array_key_exists($groupName, $multipleChoices)) {
                            $multipleChoices[$groupName] = [
                                'group' => $groupName,
                                'values' => [],
                                'isSingle' => $isSingle
                            ];
                        }

                        $multipleChoices[$groupName]['values'][] = [
                            'value' => $this->guessType($choiceItem->value),
                            'label' => $this->translator->trans($choiceItem->label, [], $translationDomain),
                            'selected' => (isset($jsonform['default']) && $this->guessType($jsonform['default']) === $this->guessType($choiceItem->value)),
                            'isSingle' => $isSingle
                        ];
                    }
                } else {
                    $isSingle = ($choiceView->data instanceof ChoiceTransformerInterface) ? $choiceView->data->isSingle() : !$multiple;
                    $choices[] = [
                        'value' => $this->guessType($choiceView->value),
                        'label' => $this->translator->trans($choiceView->label, [], $translationDomain),
                        'selected' => (isset($jsonform['default']) && $this->guessType($jsonform['default']) === $this->guessType($choiceView->value)),
                        'isSingle' => $isSingle
                    ];
                }
            }

            $schema = $this->addCommonSpecs($form, [], $extensions, $component);
            if (!empty($multipleChoices)) {
                $isGrouped = true;
                $choices = array_values($multipleChoices);
                $choices = array_map(static function (array $choice) {
                    $choice['values'] = array_map(static function (array $value) {
                        unset($value['isSingle']);
                        return $value;
                    }, $choice['values']);
                    return $choice;
                }, $choices);
                $multipleChoices = null;
                unset($multipleChoices);
            } else {
                $choices = array_map(static function (array $value) use (&$isSingle) {
                    unset($value['isSingle']);
                    return $value;
                }, $choices);
                $schema['isSingle'] = $isSingle;
            }

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
}