import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { PropertyDataTargetSettings } from "./property-data-target-settings";

export const PropertyDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "property",
            label: "Property",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <PropertyDataTargetSettings {...props} />;
            },
        });
    },
};
