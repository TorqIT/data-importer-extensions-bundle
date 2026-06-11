import React from "react";
import { LoadOrCreateDataObjectTransformerForm } from "./load-or-create-data-object-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const LoadOrCreateDataObjectTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "loadDataObject",
            label: "Load or Create Data Object",
            group: "loadImport",
            renderSettings(settings, onChange) {
                return <LoadOrCreateDataObjectTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
