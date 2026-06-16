import React from "react";
import { LoadOrCreateDataObjectTransformerForm } from "./load-or-create-data-object-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const LoadOrCreateDataObjectTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).overrideDynamicType({
            id: "loadDataObject",
            label: "Load or Create Data Object",
            group: "loadImport",
            renderSettings(settings, onChange) {
                return <LoadOrCreateDataObjectTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
