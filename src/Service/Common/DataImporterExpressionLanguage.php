<?php

declare(strict_types=1);

namespace TorqIT\DataImporterExtensionsBundle\Service\Common;

use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\ExpressionLanguage\SyntaxError;

class DataImporterExpressionLanguage extends ExpressionLanguage
{
    /**
     * @param iterable<ExpressionFunctionProviderInterface> $providers
     */
    public function __construct(
        #[AutowireIterator('pimcore.datahub.data_importer.expression_language_provider')] iterable $providers = []
    ) {
        parent::__construct();

        // Disable constant() to prevent exposure of internal information
        $this->register('constant', function () {
            throw new SyntaxError('`constant` function not available');
        }, function () {
            throw new SyntaxError('`constant` function not available');
        });

        foreach ($providers as $provider) {
            $this->registerProvider($provider);
        }
    }
}
