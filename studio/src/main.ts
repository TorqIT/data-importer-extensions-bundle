import { type IAbstractPlugin } from "@pimcore/studio-ui-bundle";
import { BulkSqlLoaderModule } from "./loaders/bulk-sql/bulk-sql-loader-module";
import { AdvancedXlsxInterpreterModule } from "./interpreters/advanced-xlsx/advanced-xlsx-interpreter-module";
import { BulkCsvInterpreterModule } from "./interpreters/bulk-csv/bulk-csv-interpreter-module";
import { BulkSqlInterpreterModule } from "./interpreters/bulk-sql/bulk-sql-interpreter-module";
import { BulkXlsxInterpreterModule } from "./interpreters/bulk-xlsx/bulk-xlsx-interpreter-module";
import { XmlSchemaBasedPreviewInterpreterModule } from "./interpreters/xml-schema-based-preview/xml-schema-based-preview-interpreter-module";
import { AdvancedClassificationStoreDataTargetModule } from "./data-targets/advanced-classification-store/advanced-classification-store-data-target-module";
import { DynamicLocalizedFieldDataTargetModule } from "./data-targets/dynamic-localized-field/dynamic-localized-field-data-target-module";
import { FieldCollectionDataTargetModule } from "./data-targets/field-collection/field-collection-data-target-module";
import { ImageGalleryAppenderDataTargetModule } from "./data-targets/image-gallery-appender/image-gallery-appender-data-target-module";
import { PropertyDataTargetModule } from "./data-targets/property/property-data-target-module";
import { TableDataTargetModule } from "./data-targets/table/table-data-target-module";
import { TagsDataTargetModule } from "./data-targets/tags/tags-data-target-module";
import { AdvancedPathLoadResolverModule } from "./resolvers/load/advanced-path/advanced-path-load-resolver-module";
import { LoadByKeyLoadResolverModule } from "./resolvers/load/load-by-key/load-by-key-load-resolver-module";
import { PropertyLoadResolverModule } from "./resolvers/load/property/property-load-resolver-module";
import { AdvancedParentCreateResolverModule } from "./resolvers/location/advanced-parent/advanced-parent-create-resolver-module";
import { AdvancedParentUpdateResolverModule } from "./resolvers/location/advanced-parent/advanced-parent-update-resolver-module";
import { ArithmeticTransformerModule } from "./transformers/arithmetic/arithmetic-transformer-module";
import { ArrayValTransformerModule } from "./transformers/array-val/array-val-transformer-module";
import { AsCountryCodeTransformerModule } from "./transformers/as-country-code/as-country-code-transformer-module";
import { AsLinkTransformerModule } from "./transformers/as-link/as-link-transformer-module";
import { AsTableTransformerModule } from "./transformers/as-table/as-table-transformer-module";
import { AsVideoTransformerModule } from "./transformers/as-video/as-video-transformer-module";
import { ConstantTransformerModule } from "./transformers/constant/constant-transformer-module";
import { EachAsArrayTransformerModule } from "./transformers/each-as-array/each-as-array-transformer-module";
import { FieldCollectionOperatorTransformerModule } from "./transformers/field-collection-operator/field-collection-operator-transformer-module";
import { ImportAssetAdvancedTransformerModule } from "./transformers/import-asset-advanced/import-asset-advanced-transformer-module";
import { LoadOrCreateDataObjectTransformerModule } from "./transformers/load-or-create-data-object/load-or-create-data-object-transformer-module";
import { QuantityValueArrayTransformerModule } from "./transformers/quantity-value-array/quantity-value-array-transformer-module";
import { QuantityValueRangeArrayTransformerModule } from "./transformers/quantity-value-range-array/quantity-value-range-array-transformer-module";
import { RegexReplaceTransformerModule } from "./transformers/regex-replace/regex-replace-transformer-module";
import { SafeKeyTransformerModule } from "./transformers/safe-key/safe-key-transformer-module";
import { SlugifyTransformerModule } from "./transformers/slugify/slugify-transformer-module";
import { SymfonyExpressionTransformerModule } from "./transformers/symfony-expression/symfony-expression-transformer-module";
import { ToClassificationStoreKvPairTransformerModule } from "./transformers/to-classification-store-key-value-pair/to-classification-store-kv-pair-transformer-module";

export default {
    name: "data-importer-extensions-plugin",

    onStartup: ({ moduleSystem }): void => {
        // interpreters
        moduleSystem.registerModule(AdvancedXlsxInterpreterModule);
        moduleSystem.registerModule(BulkCsvInterpreterModule);
        moduleSystem.registerModule(BulkSqlInterpreterModule);
        moduleSystem.registerModule(BulkXlsxInterpreterModule);
        moduleSystem.registerModule(XmlSchemaBasedPreviewInterpreterModule);

        // loaders
        moduleSystem.registerModule(BulkSqlLoaderModule);

        // data targets
        moduleSystem.registerModule(AdvancedClassificationStoreDataTargetModule);
        moduleSystem.registerModule(DynamicLocalizedFieldDataTargetModule);
        moduleSystem.registerModule(FieldCollectionDataTargetModule);
        moduleSystem.registerModule(ImageGalleryAppenderDataTargetModule);
        moduleSystem.registerModule(PropertyDataTargetModule);
        moduleSystem.registerModule(TableDataTargetModule);
        moduleSystem.registerModule(TagsDataTargetModule);

        // resolvers
        moduleSystem.registerModule(AdvancedPathLoadResolverModule);
        moduleSystem.registerModule(LoadByKeyLoadResolverModule);
        moduleSystem.registerModule(PropertyLoadResolverModule);
        moduleSystem.registerModule(AdvancedParentCreateResolverModule);
        moduleSystem.registerModule(AdvancedParentUpdateResolverModule);

        // transformers
        moduleSystem.registerModule(ArithmeticTransformerModule);
        moduleSystem.registerModule(ArrayValTransformerModule);
        moduleSystem.registerModule(AsCountryCodeTransformerModule);
        moduleSystem.registerModule(AsLinkTransformerModule);
        moduleSystem.registerModule(AsTableTransformerModule);
        moduleSystem.registerModule(AsVideoTransformerModule);
        moduleSystem.registerModule(ConstantTransformerModule);
        moduleSystem.registerModule(EachAsArrayTransformerModule);
        moduleSystem.registerModule(FieldCollectionOperatorTransformerModule);
        moduleSystem.registerModule(ImportAssetAdvancedTransformerModule);
        moduleSystem.registerModule(LoadOrCreateDataObjectTransformerModule);
        moduleSystem.registerModule(QuantityValueArrayTransformerModule);
        moduleSystem.registerModule(QuantityValueRangeArrayTransformerModule);
        moduleSystem.registerModule(RegexReplaceTransformerModule);
        moduleSystem.registerModule(SafeKeyTransformerModule);
        moduleSystem.registerModule(SlugifyTransformerModule);
        moduleSystem.registerModule(SymfonyExpressionTransformerModule);
        moduleSystem.registerModule(ToClassificationStoreKvPairTransformerModule);
    },
} as IAbstractPlugin;
