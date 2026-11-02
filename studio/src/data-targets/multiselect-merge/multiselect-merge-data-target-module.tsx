import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { MultiselectMergeDataTargetSettings } from "./multiselect-merge-data-target-settings";

export const MultiselectMergeDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "multiselectMerge",
            label: "Multiselect Merge",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <MultiselectMergeDataTargetSettings { ...props } />;
            },
        });
    },
};
