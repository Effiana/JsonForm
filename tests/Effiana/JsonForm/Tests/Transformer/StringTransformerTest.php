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

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Effiana\JsonForm\Transformer\CompoundTransformer;
use Effiana\JsonForm\Transformer\StringTransformer;
use Effiana\JsonForm\Resolver;
use Effiana\JsonForm\Tests\JsonFormTestCase;

/**
 * @author Nacho Martín <nacho@limenius.com>
 *
 * @see TypeTestCase
 */
class StringTransformerTest extends JsonFormTestCase
{
    public function testPattern()
    {
        $form = $this->factory->create(FormType::class)
            ->add(
                'firstName',
                TextType::class,
                ['attr' => ['pattern' => '.{5,}' ]]
            );
        $resolver = new Resolver();
        $resolver->setTransformer('text', new StringTransformer($this->translator));
        $transformer = new CompoundTransformer($this->translator, null, $resolver);
        $transformed = $transformer->transform($form);
        $this->assertTrue(is_array($transformed));
        $this->assertEquals('.{5,}', $transformed['properties']['firstName']['pattern']);
    }
}
