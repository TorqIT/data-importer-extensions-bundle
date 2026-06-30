import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { DynamicLocalizedFieldDataTargetSettings } from "./dynamic-localized-field-data-target-settings";

export const DynamicLocalizedFieldDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "dynamicLocalizedField",
            label: "Dynamic Localized Field",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <DynamicLocalizedFieldDataTargetSettings {...props} />;
            },
        });
    },
};
