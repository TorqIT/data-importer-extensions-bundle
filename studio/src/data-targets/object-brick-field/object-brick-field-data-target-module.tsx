import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { ObjectBrickFieldDataTargetSettings } from "./object-brick-field-data-target-settings";

export const ObjectBrickFieldDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "objectBrickField",
            label: "Object Brick Field",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <ObjectBrickFieldDataTargetSettings { ...props } />;
            },
        });
    },
};
