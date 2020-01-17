<?php
/**
 * This file is part of the Effiana package.
 *
 * (c) Effiana, LTD
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Dominik Labudzinski <dominik@labudzinski.com>
 */
declare(strict_types=1);

namespace Effiana\JsonForm\Transformer {

    use Symfony\Component\Form\FormInterface;

    /**
     * Class HiddenTransformer
     * @package Effiana\JsonForm\Transformer
     */
    class HiddenTransformer extends AbstractTransformer
    {
        /**
         * Given that this transformation will only be called when the type is a hidden, it allows us to define how to build
         * the schema.
         *
         * {@inheritdoc}
         */
        public function transform(FormInterface $form, array $extensions = [], $widget = null): array
        {
            $schema = ['type' => 'string'];
            $schema = $this->addCommonSpecs($form, $schema, $extensions, $widget);

            return $schema;
        }
    }
}
