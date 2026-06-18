import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { AdvancedClassificationStoreDataTargetSettings } from "./advanced-classification-store-data-target-settings";

export const AdvancedClassificationStoreDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "advancedClassificationStore",
            label: "Advanced Classification Store",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <AdvancedClassificationStoreDataTargetSettings {...props} />;
            },
        });
    },
};
