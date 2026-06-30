import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getResolverRegistry } from "../../../common/consts/registries";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";
import { AdvancedPathLoadResolverSettings } from "./advanced-path-load-resolver-settings";

export const AdvancedPathLoadResolverModule: AbstractModule = {
    onInit() {
        getResolverRegistry(container).registerDynamicType({
            id: "loading.advancedPath",
            type: "advancedPath",
            label: "Advanced Path",
            group: "loading",
            renderSettings(props: DynamicTypeResolverRenderProps) {
                return <AdvancedPathLoadResolverSettings {...props} />;
            },
        });
    },
};
