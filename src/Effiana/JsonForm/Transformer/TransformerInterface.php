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

namespace Effiana\JsonForm\Transformer;

use Symfony\Component\Form\FormInterface;

/**
 * @author Nacho Martín <nacho@limenius.com>
 */
interface TransformerInterface
{
    /**
     * @param FormInterface        $form
     * @param ExtensionInterface[] $extensions
     * @param string|null          $component
     *
     * @return array
     */
    public function transform(FormInterface $form, array $extensions = [], $component = null): array;
}
