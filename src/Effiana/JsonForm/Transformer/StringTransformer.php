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

namespace Effiana\JsonForm\Transformer;

use Effiana\JsonForm\FormUtil;
use Effiana\JsonForm\Guesser\ValidatorGuesser;
use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class StringTransformer extends AbstractTransformer
{
    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $component = null): array
    {
        $schema = [];
        $schema = $this->addCommonSpecs($form, $schema, $extensions, $component);
//        $schema = $this->addMaxLength($form, $schema);
//        $schema = $this->addMinLength($form, $schema);

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addMaxLength(FormInterface $form, array $schema): array
    {
        if (($attr = $form->getConfig()->getOption('attr')) && isset($attr['maxlength'])) {
            $schema['maxLength'] = $attr['maxlength'];
        }

        return $schema;
    }

    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    protected function addMinLength(FormInterface $form, array $schema): array
    {
        if (($attr = $form->getConfig()->getOption('attr')) && isset($attr['minlength'])) {
            $schema['minLength'] = $attr['minlength'];

            return $schema;
        }
        
        if (null === $this->validatorGuesser) {
            return $schema;
        }

        $class = FormUtil::findDataClass($form);

        if (null === $class) {
            return $schema;
        }

        $minLengthGuess = $this->validatorGuesser->guessMinLength($class, $form->getName());
        $minLength = $minLengthGuess ? $minLengthGuess->getValue() : null;

        if ($minLength) {
            $schema['minLength'] = $minLength;
        }

        return $schema;
    }
}
