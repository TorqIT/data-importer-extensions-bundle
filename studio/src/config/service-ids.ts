// Reference to the core data-importer transformer registry (owned by pimcore/data-importer)
export const coreServiceIds = {
    'DataImporter/DynamicTypes/Transformer/Registry': 'DataImporter/DynamicTypes/Transformer/Registry',
} as const

// Service IDs for our extension transformers
export const bundleServiceIds = {
    // Net-new operators
    'DataImporterExtensions/DynamicTypes/Transformer/Arithmetic': 'DataImporterExtensions/DynamicTypes/Transformer/Arithmetic',
    'DataImporterExtensions/DynamicTypes/Transformer/ArrayVal': 'DataImporterExtensions/DynamicTypes/Transformer/ArrayVal',
    'DataImporterExtensions/DynamicTypes/Transformer/AsCountryCode': 'DataImporterExtensions/DynamicTypes/Transformer/AsCountryCode',
    'DataImporterExtensions/DynamicTypes/Transformer/AsLink': 'DataImporterExtensions/DynamicTypes/Transformer/AsLink',
    'DataImporterExtensions/DynamicTypes/Transformer/AsTable': 'DataImporterExtensions/DynamicTypes/Transformer/AsTable',
    'DataImporterExtensions/DynamicTypes/Transformer/AsVideo': 'DataImporterExtensions/DynamicTypes/Transformer/AsVideo',
    'DataImporterExtensions/DynamicTypes/Transformer/Constant': 'DataImporterExtensions/DynamicTypes/Transformer/Constant',
    'DataImporterExtensions/DynamicTypes/Transformer/EachAsArray': 'DataImporterExtensions/DynamicTypes/Transformer/EachAsArray',
    'DataImporterExtensions/DynamicTypes/Transformer/FieldCollectionOperator': 'DataImporterExtensions/DynamicTypes/Transformer/FieldCollectionOperator',
    'DataImporterExtensions/DynamicTypes/Transformer/ImportAssetAdvanced': 'DataImporterExtensions/DynamicTypes/Transformer/ImportAssetAdvanced',
    'DataImporterExtensions/DynamicTypes/Transformer/QuantityValueRangeArray': 'DataImporterExtensions/DynamicTypes/Transformer/QuantityValueRangeArray',
    'DataImporterExtensions/DynamicTypes/Transformer/RegexReplace': 'DataImporterExtensions/DynamicTypes/Transformer/RegexReplace',
    'DataImporterExtensions/DynamicTypes/Transformer/SafeKey': 'DataImporterExtensions/DynamicTypes/Transformer/SafeKey',
    'DataImporterExtensions/DynamicTypes/Transformer/Slugify': 'DataImporterExtensions/DynamicTypes/Transformer/Slugify',
    'DataImporterExtensions/DynamicTypes/Transformer/SymfonyExpression': 'DataImporterExtensions/DynamicTypes/Transformer/SymfonyExpression',
    'DataImporterExtensions/DynamicTypes/Transformer/ToClassificationStoreKeyValuePair': 'DataImporterExtensions/DynamicTypes/Transformer/ToClassificationStoreKeyValuePair',

    // Overrides of core transformers (same PHP type ID, different DI service key)
    'DataImporterExtensions/DynamicTypes/Transformer/LoadOrCreateDataObject': 'DataImporterExtensions/DynamicTypes/Transformer/LoadOrCreateDataObject',
    'DataImporterExtensions/DynamicTypes/Transformer/QuantityValueArray': 'DataImporterExtensions/DynamicTypes/Transformer/QuantityValueArray',
} as const
