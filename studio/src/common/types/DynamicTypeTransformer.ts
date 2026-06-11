import React from "react";
import { DynamicTypeAbstract } from "@pimcore/studio-ui-bundle/modules/element";

export type TransformerGroup = "dataManipulation" | "dataTypes" | "loadImport";

/** Facade to match the DynamicTypeTransformerAbstract from data-importer until they provide an npm package with types */
export interface DynamicTypeTransformer extends DynamicTypeAbstract {
    readonly label: string;
    readonly group: TransformerGroup;
    renderSettings(
        settings: Record<string, any>,
        onChange: (settings: Record<string, any>) => void,
    ): React.JSX.Element | null;
}
