import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getResolverRegistry } from "../../../common/consts/registries";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";
import { AdvancedParentCreateResolverSettings } from "./advanced-parent-create-resolver-settings";

export const AdvancedParentCreateResolverModule: AbstractModule = {
    onInit() {
        getResolverRegistry(container).registerDynamicType({
            id: "createLocation.advancedParent",
            type: "advancedParent",
            label: "Advanced Parent",
            group: "createLocation",
            renderSettings(props: DynamicTypeResolverRenderProps) {
                return <AdvancedParentCreateResolverSettings {...props} />;
            },
        });
    },
};
