import { type IAbstractPlugin } from "@pimcore/studio-ui-bundle";
import { ArithmeticTransformerModule } from "./transformers/arithmetic/arithmetic-transformer-module";
import { ArrayValTransformerModule } from "./transformers/array-val/array-val-transformer-module";
import { AsTableTransformerModule } from "./transformers/as-table/as-table-transformer-module";
import { AsVideoTransformerModule } from "./transformers/as-video/as-video-transformer-module";
import { ConstantTransformerModule } from "./transformers/constant/constant-transformer-module";
import { FieldCollectionOperatorTransformerModule } from "./transformers/field-collection-operator/field-collection-operator-transformer-module";
import { ImportAssetAdvancedTransformerModule } from "./transformers/import-asset-advanced/import-asset-advanced-transformer-module";
import { LoadOrCreateDataObjectTransformerModule } from "./transformers/load-or-create-data-object/load-or-create-data-object-transformer-module";
import { QuantityValueArrayTransformerModule } from "./transformers/quantity-value-array/quantity-value-array-transformer-module";
import { QuantityValueRangeArrayTransformerModule } from "./transformers/quantity-value-range-array/quantity-value-range-array-transformer-module";
import { RegexReplaceTransformerModule } from "./transformers/regex-replace/regex-replace-transformer-module";
import { SymfonyExpressionTransformerModule } from "./transformers/symfony-expression/symfony-expression-transformer-module";
import { ToClassificationStoreKvPairTransformerModule } from "./transformers/to-classification-store-key-value-pair/to-classification-store-kv-pair-transformer-module";

export default {
    name: "data-importer-extensions-plugin",

    onStartup: ({ moduleSystem }): void => {
        moduleSystem.registerModule(ArithmeticTransformerModule);
        moduleSystem.registerModule(ArrayValTransformerModule);
        moduleSystem.registerModule(AsTableTransformerModule);
        moduleSystem.registerModule(AsVideoTransformerModule);
        moduleSystem.registerModule(ConstantTransformerModule);
        moduleSystem.registerModule(FieldCollectionOperatorTransformerModule);
        moduleSystem.registerModule(ImportAssetAdvancedTransformerModule);
        moduleSystem.registerModule(LoadOrCreateDataObjectTransformerModule);
        moduleSystem.registerModule(QuantityValueArrayTransformerModule);
        moduleSystem.registerModule(QuantityValueRangeArrayTransformerModule);
        moduleSystem.registerModule(RegexReplaceTransformerModule);
        moduleSystem.registerModule(SymfonyExpressionTransformerModule);
        moduleSystem.registerModule(ToClassificationStoreKvPairTransformerModule);
    },
} as IAbstractPlugin;
