import { DynamicTypeAbstract, DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import React from "react";

type TransformerGroup = "dataManipulation" | "dataTypes" | "loadImport";

/** Facade to match the DynamicTypeTransformerAbstract from data-importer until they provide an npm package with types */
interface DynamicTypeTransformer extends DynamicTypeAbstract {
    readonly label: string;
    readonly group: TransformerGroup;

    renderSettings(
        settings: Record<string, any>,
        onChange: (settings: Record<string, any>) => void,
    ): React.JSX.Element | null;
}

/** Facade to match the registry from data-importer until they provide an npm package with types */
export interface DynamicTypeTransformerRegistry extends DynamicTypeRegistryAbstract<DynamicTypeTransformer> {}
