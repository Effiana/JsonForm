<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Effiana\JsonForm\Tests\Liform\Transformer;

use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Effiana\JsonForm\Transformer\CompoundTransformer;
use Effiana\JsonForm\Transformer\NumberTransformer;
use Effiana\JsonForm\Resolver;
use Effiana\JsonForm\Tests\JsonFormTestCase;

/**
 * @author Nacho Martín <nacho@limenius.com>
 *
 * @see TypeTestCase
 */
class NumberTransformerTest extends JsonFormTestCase
{
    public function testPattern()
    {
        $form = $this->factory->create(FormType::class)
            ->add(
                'somefield',
                NumberType::class,
                ['jsonform' => ['widget' => 'widget']]
            );
        $resolver = new Resolver();
        $resolver->setTransformer('number', new NumberTransformer($this->translator));
        $transformer = new CompoundTransformer($this->translator, null, $resolver);
        $transformed = $transformer->transform($form);
        $this->assertTrue(is_array($transformed));
        $this->assertEquals('number', $transformed['properties']['somefield']['type']);
        $this->assertEquals('widget', $transformed['properties']['somefield']['widget']);
    }
}
