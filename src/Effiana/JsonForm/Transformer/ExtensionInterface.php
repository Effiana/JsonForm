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

use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
interface ExtensionInterface
{
    /**
     * @param FormInterface $form
     * @param array         $schema
     *
     * @return array
     */
    public function apply(FormInterface $form, array $schema): array;
}
