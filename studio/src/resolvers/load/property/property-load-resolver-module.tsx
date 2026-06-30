import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getResolverRegistry } from "../../../common/consts/registries";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";
import { PropertyLoadResolverSettings } from "./property-load-resolver-settings";

export const PropertyLoadResolverModule: AbstractModule = {
    onInit() {
        getResolverRegistry(container).registerDynamicType({
            id: "loading.property",
            type: "property",
            label: "Property",
            group: "loading",
            renderSettings(props: DynamicTypeResolverRenderProps) {
                return <PropertyLoadResolverSettings {...props} />;
            },
        });
    },
};
