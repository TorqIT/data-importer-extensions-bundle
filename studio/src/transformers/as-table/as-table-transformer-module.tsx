import React from "react";
import { AsTableTransformerForm } from "./as-table-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const AsTableTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "asTable",
            label: "As Table",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <AsTableTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
