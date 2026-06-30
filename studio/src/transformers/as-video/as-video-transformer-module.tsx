import React from "react";
import { AsVideoTransformerForm } from "./as-video-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const AsVideoTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "asVideo",
            label: "As Video",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <AsVideoTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
