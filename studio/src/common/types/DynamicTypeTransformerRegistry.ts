import { DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import { DynamicTypeTransformer } from "./DynamicTypeTransformer";

/** Facade to match the registry from data-importer until they provide an npm package with types */
export interface DynamicTypeTransformerRegistry extends DynamicTypeRegistryAbstract<DynamicTypeTransformer> {}
