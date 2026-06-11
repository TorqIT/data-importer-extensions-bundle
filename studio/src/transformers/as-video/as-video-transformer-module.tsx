import React from "react";
import { AsVideoTransformerForm } from "./as-video-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const AsVideoTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "asVideo",
            label: "As Video",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <AsVideoTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
