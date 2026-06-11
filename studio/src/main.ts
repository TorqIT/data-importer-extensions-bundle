import { type IAbstractPlugin } from "@pimcore/studio-ui-bundle";
import { ArithmeticTransformerModule } from "./transformers/arithmetic/arithmetic-transformer-module";
import { ArrayValTransformerModule } from "./transformers/array-val/array-val-transformer-module";
import { AsTableTransformerModule } from "./transformers/as-table/as-table-transformer-module";
import { AsVideoTransformerModule } from "./transformers/as-video/as-video-transformer-module";
import { ConstantTransformerModule } from "./transformers/constant/constant-transformer-module";
import { FieldCollectionOperatorTransformerModule } from "./transformers/field-collection-operator/field-collection-operator-transformer-module";

export default {
    name: "data-importer-extensions-plugin",

    onStartup: ({ moduleSystem }): void => {
        moduleSystem.registerModule(ArithmeticTransformerModule);
        moduleSystem.registerModule(ArrayValTransformerModule);
        moduleSystem.registerModule(AsTableTransformerModule);
        moduleSystem.registerModule(AsVideoTransformerModule);
        moduleSystem.registerModule(ConstantTransformerModule);
        moduleSystem.registerModule(FieldCollectionOperatorTransformerModule);
    },
} as IAbstractPlugin;
