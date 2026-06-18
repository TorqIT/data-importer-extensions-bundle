import { DynamicTypeTransformerRegistry } from "../types/DynamicTypeTransformerRegistry";
import { container as studioContainer } from "@pimcore/studio-ui-bundle";
import { DynamicTypeInterpreterRegistry } from "../types/DynamicTypeInterpreterRegistry";
import { DynamicTypeLoaderRegistry } from "../types/DynamicTypeLoaderRegistry";
import { DynamicTypeDataTargetRegistry } from "../types/DynamicTypeDataTargetRegistry";
import { DynamicTypeResolverRegistry } from "../types/DynamicTypeResolverRegistry";

export function getTransformerRegistry(container: typeof studioContainer) {
    return container.get<DynamicTypeTransformerRegistry>("DataImporter/DynamicTypes/Transformer/Registry");
}

export function getInterpreterRegistry(container: typeof studioContainer) {
    return container.get<DynamicTypeInterpreterRegistry>("DataImporter/DynamicTypes/Interpreter/Registry");
}

export function getLoaderRegistry(container: typeof studioContainer) {
    return container.get<DynamicTypeLoaderRegistry>("DataImporter/DynamicTypes/Loader/Registry");
}

export function getDataTargetRegistry(container: typeof studioContainer) {
    return container.get<DynamicTypeDataTargetRegistry>("DataImporter/DynamicTypes/DataTarget/Registry");
}

export function getResolverRegistry(container: typeof studioContainer) {
    return container.get<DynamicTypeResolverRegistry>("DataImporter/DynamicTypes/Resolver/Registry");
}
