import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getDataTargetRegistry } from "../../common/consts/registries";
import { type DynamicTypeDataTargetRenderProps } from "../../common/types/DynamicTypeDataTargetRegistry";
import { TagsDataTargetSettings } from "./tags-data-target-settings";

export const TagsDataTargetModule: AbstractModule = {
    onInit() {
        getDataTargetRegistry(container).registerDynamicType({
            id: "tags",
            label: "Tags",
            supportsType() {
                return true;
            },
            renderSettings(props: DynamicTypeDataTargetRenderProps) {
                return <TagsDataTargetSettings {...props} />;
            },
        });
    },
};
