import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getResolverRegistry } from "../../../common/consts/registries";
import { type DynamicTypeResolverRenderProps } from "../../../common/types/DynamicTypeResolverRegistry";
import { LoadByKeyLoadResolverSettings } from "./load-by-key-load-resolver-settings";

export const LoadByKeyLoadResolverModule: AbstractModule = {
    onInit() {
        getResolverRegistry(container).registerDynamicType({
            id: "loading.load_by_key",
            label: "Load by Key",
            group: "loading",
            renderSettings(props: DynamicTypeResolverRenderProps) {
                return <LoadByKeyLoadResolverSettings {...props} />;
            },
        });
    },
};
