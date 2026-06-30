import { DynamicTypeAbstract, DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import React from "react";

export interface DynamicTypeLoader extends DynamicTypeAbstract {
    readonly label: string;
    renderSettings(configName: string): React.JSX.Element | null;
}

/** Facade to match the registry from data-importer until they provide an npm package with types */
export interface DynamicTypeLoaderRegistry extends DynamicTypeRegistryAbstract<DynamicTypeLoader> {}
