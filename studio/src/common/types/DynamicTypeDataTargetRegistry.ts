import { DynamicTypeAbstract, DynamicTypeRegistryAbstract } from "@pimcore/studio-ui-bundle/modules/element";
import React from "react";

export interface DataTargetConfigSettings {
    type?: string;
    settings?: {
        fieldName?: string;
        language?: string;
        writeIfTargetIsNotEmpty?: boolean;
        writeIfSourceIsEmpty?: boolean;
        [key: string]: any;
    };
}

export interface DynamicTypeDataTargetRenderProps {
    classId?: string;
    classFieldOptions: Array<{ value: string; label: string }>;
    isLocalized: boolean;
    transformationResultType?: string;
    settings: DataTargetConfigSettings;
    onChange: (settings: DataTargetConfigSettings) => void;
}

export interface DynamicTypeDataTarget extends DynamicTypeAbstract {
    readonly label: string;
    supportsType(type?: string): boolean;
    renderSettings(props: DynamicTypeDataTargetRenderProps): React.JSX.Element | null;
}

/** Facade to match the registry from data-importer until they provide an npm package with types */
export interface DynamicTypeDataTargetRegistry extends DynamicTypeRegistryAbstract<DynamicTypeDataTarget> {}
