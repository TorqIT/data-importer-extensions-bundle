import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getResolverRegistry } from "../../../common/consts/registries";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";
import { AdvancedParentUpdateResolverSettings } from "./advanced-parent-update-resolver-settings";

export const AdvancedParentUpdateResolverModule: AbstractModule = {
    onInit() {
        getResolverRegistry(container).registerDynamicType({
            id: "updateLocation.advancedParent",
            label: "Advanced Parent",
            group: "updateLocation",
            renderSettings(props: DynamicTypeResolverRenderProps) {
                return <AdvancedParentUpdateResolverSettings {...props} />;
            },
        });
    },
};
