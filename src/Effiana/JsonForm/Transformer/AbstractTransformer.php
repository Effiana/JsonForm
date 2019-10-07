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
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
abstract class AbstractTransformer implements TransformerInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var FormTypeGuesserInterface|null
     */
    protected $validatorGuesser;

    /**
     * @param TranslatorInterface           $translator
     * @param FormTypeGuesserInterface|null $validatorGuesser
     */
    public function __construct(TranslatorInterface $translator, FormTypeGuesserInterface $validatorGuesser = null)
    {
        $this->translator = $translator;
        $this->validatorGuesser = $validatorGuesser;
    }

    /**
     * @param ExtensionInterface[] $extensions
     * @param FormInterface        $form
     * @param array                $schema
     *
     * @return array
     */
    protected function applyExtensions(array $extensions, FormInterface $form, array $schema): array
    {
        $newSchema = $schema;
        foreach ($extensions as $extension) {
            $newSchema = $extension->apply($form, $newSchema);
        }

        return $newSchema;
    }

    /**
     * @param FormInterface        $form
     * @param array                $schema
     * @param ExtensionInterface[] $extensions
     * @param string               $component
     *
     * @return array
     */
    protected function addCommonSpecs(FormInterface $form, array $schema, $extensions = [], $component): array
    {
        $schema = $this->addLabel($form, $schema);
        $schema = $this->addPattern($form, $schema);
        $schema = $this->addDescription($form, $schema);
        $schema = $this->addComponent($form, $schema, $component);
        $schema = $this->applyExtensions($extensions, $form, $schema);

        return $schema;
    }


    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addPattern(FormInterface $form, array $schema): array
    {
        if ($attr = $form->getConfig()->getOption('attr')) {
            if (isset($attr['pattern'])) {
                $schema['pattern'] = $attr['pattern'];
            }
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addLabel(FormInterface $form, array $schema): array
    {
        $translationDomain = $form->getConfig()->getOption('translation_domain');
        if ($label = $form->getConfig()->getOption('label')) {
            $schema['label'] = $this->translator->trans($label, [], $translationDomain);
        } else {
            $schema['label'] = $this->translator->trans($form->getName(), [], $translationDomain);
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addDescription(FormInterface $form, array $schema): array
    {
        if ($jsonform = $form->getConfig()->getOption('jsonform')) {
            if (isset($jsonform['description']) && $description = $jsonform['description']) {
                $schema['description'] = $this->translator->trans($description);
            }
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     * @param mixed         $configComponent
     *
     * @return array
     */
    protected function addComponent(FormInterface $form, array $schema, $configComponent): array
    {

        if ($jsonform = $form->getConfig()->getOption('jsonform')) {
            if (isset($jsonform['component']) && $component = $jsonform['component']) {
                $schema['component'] = $component;
            }
        } elseif ($configComponent) {
            $schema['component'] = $configComponent;
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     *
     * @return boolean
     */
    protected function isRequired(FormInterface $form): bool
    {
        return $form->getConfig()->getOption('required');
    }
}
