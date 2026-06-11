import { DynamicTypeTransformerRegistry } from "../types/DynamicTypeTransformerRegistry";
import { container } from "@pimcore/studio-ui-bundle";
import { DynamicTypeInterpreterRegistry } from "../types/DynamicTypeInterpreterRegistry";
import { DynamicTypeLoaderRegistry } from "../types/DynamicTypeLoaderRegistry";
import { DynamicTypeDataTargetRegistry } from "../types/DynamicTypeDataTargetRegistry";

export const transformerRegistry = container.get<DynamicTypeTransformerRegistry>(
    "DataImporter/DynamicTypes/Transformer/Registry",
);

export const interpreterRegistry = container.get<DynamicTypeInterpreterRegistry>(
    "DataImporter/DynamicTypes/Interpreter/Registry",
);

export const loaderRegistry = container.get<DynamicTypeLoaderRegistry>("DataImporter/DynamicTypes/Loader/Registry");

export const dataTargetRegistry = container.get<DynamicTypeDataTargetRegistry>(
    "DataImporter/DynamicTypes/DataTarget/Registry",
);
