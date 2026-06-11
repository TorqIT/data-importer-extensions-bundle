import { DynamicTypeTransformerRegistry } from "../types/DynamicTypeTransformerRegistry";
import { container } from "@pimcore/studio-ui-bundle";

export const transformerRegistry = container.get<DynamicTypeTransformerRegistry>(
    "DataImporter/DynamicTypes/Transformer/Registry",
);
