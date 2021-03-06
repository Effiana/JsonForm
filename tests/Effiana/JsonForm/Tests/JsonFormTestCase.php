<?php

/*
 * This file is part of the Effiana\JsonForm package.
 *
 * (c) Effiana <https://github.com/Limenius/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Effiana\JsonForm\Tests;

use Effiana\JsonForm\Form\Extension\AddJsonFormExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Forms;
use Symfony\Component\Translation\TranslatorInterface;

/**
 *
 * @author Nacho Martín <nacho@limenius.com>
 *
 * @see TestCase
 */
class JsonFormTestCase extends TestCase
{
    /**
     * @var FormFactoryInterface
     */
    protected $factory;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    protected function setUp()
    {
        $ext = new AddJsonFormExtension();
        $this->factory = Forms::createFormFactoryBuilder()
            ->addExtensions([])
            ->addTypeExtensions([$ext])
            ->getFormFactory();

        $this->translator = $this->createMock(TranslatorInterface::class);
    }
}
