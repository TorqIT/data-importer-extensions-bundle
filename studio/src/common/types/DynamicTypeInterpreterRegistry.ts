import { DynamicTypeAbstract, DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import React from "react";

export interface DynamicTypeInterpreter extends DynamicTypeAbstract {
    readonly label: string;
    renderSettings(): React.JSX.Element | null;
}

/** Facade to match the registry from data-importer until they provide an npm package with types */
export interface DynamicTypeInterpreterRegistry extends DynamicTypeRegistryAbstract<DynamicTypeInterpreter> {}
