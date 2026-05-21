import { container, type AbstractModule } from '@pimcore/studio-ui-bundle'
import { bundleServiceIds, coreServiceIds } from '../../config/service-ids'
import {
    DynamicTypeTransformerEachAsArray,
    DynamicTypeTransformerSafeKey,
    DynamicTypeTransformerSlugify,
    DynamicTypeTransformerAsCountryCode,
    DynamicTypeTransformerAsLink,
    DynamicTypeTransformerArithmetic,
    DynamicTypeTransformerArrayVal,
    DynamicTypeTransformerAsTable,
    DynamicTypeTransformerAsVideo,
    DynamicTypeTransformerConstant,
    DynamicTypeTransformerFieldCollectionOperator,
    DynamicTypeTransformerImportAssetAdvanced,
    DynamicTypeTransformerRegexReplace,
    DynamicTypeTransformerSymfonyExpression,
    DynamicTypeTransformerToClassificationStoreKvPair,
    DynamicTypeTransformerLoadOrCreateDataObject,
    DynamicTypeTransformerQuantityValueArray,
    DynamicTypeTransformerQuantityValueRangeArray,
} from './dynamic-types/transformer'

export const DataImporterExtensionsModule: AbstractModule = {
    onInit: (): void => {
        // No-settings transformers
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/EachAsArray']).to(DynamicTypeTransformerEachAsArray).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/SafeKey']).to(DynamicTypeTransformerSafeKey).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Slugify']).to(DynamicTypeTransformerSlugify).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsCountryCode']).to(DynamicTypeTransformerAsCountryCode).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsLink']).to(DynamicTypeTransformerAsLink).inSingletonScope()

        // Transformers with settings
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Arithmetic']).to(DynamicTypeTransformerArithmetic).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ArrayVal']).to(DynamicTypeTransformerArrayVal).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsTable']).to(DynamicTypeTransformerAsTable).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsVideo']).to(DynamicTypeTransformerAsVideo).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Constant']).to(DynamicTypeTransformerConstant).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/FieldCollectionOperator']).to(DynamicTypeTransformerFieldCollectionOperator).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ImportAssetAdvanced']).to(DynamicTypeTransformerImportAssetAdvanced).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/RegexReplace']).to(DynamicTypeTransformerRegexReplace).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/SymfonyExpression']).to(DynamicTypeTransformerSymfonyExpression).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ToClassificationStoreKeyValuePair']).to(DynamicTypeTransformerToClassificationStoreKvPair).inSingletonScope()

        // Core overrides (unique service IDs, but transformer id matches core — overwrites registry entry)
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/LoadOrCreateDataObject']).to(DynamicTypeTransformerLoadOrCreateDataObject).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/QuantityValueArray']).to(DynamicTypeTransformerQuantityValueArray).inSingletonScope()
        container.bind(bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/QuantityValueRangeArray']).to(DynamicTypeTransformerQuantityValueRangeArray).inSingletonScope()

        // Register all into the core transformer registry (runs after core module, so overrides win)
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        const transformerRegistry = container.get<any>(coreServiceIds['DataImporter/DynamicTypes/Transformer/Registry'])

        const allServiceIds = [
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/EachAsArray'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/SafeKey'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Slugify'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsCountryCode'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsLink'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Arithmetic'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ArrayVal'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsTable'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/AsVideo'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/Constant'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/FieldCollectionOperator'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ImportAssetAdvanced'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/RegexReplace'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/SymfonyExpression'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/ToClassificationStoreKeyValuePair'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/LoadOrCreateDataObject'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/QuantityValueArray'],
            bundleServiceIds['DataImporterExtensions/DynamicTypes/Transformer/QuantityValueRangeArray'],
        ] as const

        for (const serviceId of allServiceIds) {
            transformerRegistry.registerDynamicType(container.get(serviceId))
        }
    },
}
