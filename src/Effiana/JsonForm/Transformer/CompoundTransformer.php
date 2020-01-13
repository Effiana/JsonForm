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

use Effiana\JsonForm\FormUtil;
use Effiana\JsonForm\ResolverInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeGuesserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
class CompoundTransformer extends AbstractTransformer
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
        FormTypeGuesserInterface $validatorGuesser = null,
        ResolverInterface $resolver
    ) {
        parent::__construct($translator, $validatorGuesser);
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function transform(FormInterface $form, array $extensions = [], $component = null): array
    {
        $schema = [];

        foreach ($form->all() as $name => $field) {
            $options = $field->getConfig()->getOptions();
            $jsonFormOptions = $options['jsonform'] ?? [];
            $jsonFormOptions['column'] = $jsonFormOptions['column'] ?? 0;
            $jsonFormOptions['tab'] = $jsonFormOptions['tab'] ?? 'main';
            $jsonFormOptions['constraints'] = $jsonFormOptions['constraints'] ?? [];
            $transformerData = $this->resolver->resolve($field);

            $transformedChild = $transformerData['transformer']->transform($field, $extensions, $transformerData['component']);
//            $transformedChild['propertyOrder'] = $order;

            $transformedChild['name'] = $name;
            if(array_key_exists('constraints', $jsonFormOptions)) {
                $transformedChild['constraints'] = $jsonFormOptions['constraints'];
            }
            if(array_key_exists('mode', $jsonFormOptions)) {
                $transformedChild['mode'] = $jsonFormOptions['mode'];
            }
            $componentName = $transformedChild['component'] ?? $name;
            unset($transformedChild['component']);

            $transformedChild['required'] = $transformerData['transformer']->isRequired($field);
            if(!array_key_exists($jsonFormOptions['column'], $schema)) {
                $schema[$jsonFormOptions['column']] = [
                    'column' => $jsonFormOptions['column']
                ];
            }

            if(!array_key_exists($jsonFormOptions['tab'], $schema[$jsonFormOptions['column']])) {
                $schema[$jsonFormOptions['column']][$jsonFormOptions['tab']] = [
                    'tab' => $jsonFormOptions['tab'],
                    'components' => []
                ];
            }
            if(!array_key_exists($componentName, $schema[$jsonFormOptions['column']][$jsonFormOptions['tab']]['components'])) {
                $schema[$jsonFormOptions['column']][$jsonFormOptions['tab']]['components'][$componentName] = [
                    'component' => $componentName,
                    'fields' => []
                ];
            }
            $schema[$jsonFormOptions['column']][$jsonFormOptions['tab']]['components'][$componentName]['fields'][] = $transformedChild;
        }
        foreach ($schema as $key => $value) {
            foreach ($value as $k => $v) {
                if(isset($v['components'])) {
                    $schema[$key][$k]['components'] = array_values($v['components']);
                }
            }
        }

        $innerType = $form->getConfig()->getType()->getInnerType();

        $schema = $this->addCommonSpecs($form, $schema, $extensions, $component);
        unset($schema['label']);
        if (method_exists($innerType, 'buildJsonForm')) {
            $schema = $innerType->buildJsonForm($form, $schema);
        }
        $schema = array_values($schema);
        $schema = array_filter($schema, static function($item) {
            return is_array($item);
        });

        $schema = array_map(static function($item) {
            if(array_key_exists('column', $item)) {
                unset($item['column']);
            }
            if(is_array($item)) {
                return array_values($item);
            }
        }, $schema);

        return [
            'data' => $schema
        ];
    }
}
