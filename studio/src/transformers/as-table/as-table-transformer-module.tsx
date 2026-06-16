import React from "react";
import { AsTableTransformerForm } from "./as-table-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const AsTableTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "asTable",
            label: "As Table",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <AsTableTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
