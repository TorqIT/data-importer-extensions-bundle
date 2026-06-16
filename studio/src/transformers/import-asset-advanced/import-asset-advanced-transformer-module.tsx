import React from "react";
import { ImportAssetAdvancedTransformerForm } from "./import-asset-advanced-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const ImportAssetAdvancedTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "importAssetAdvanced",
            label: "Import Asset Advanced",
            group: "loadImport",
            renderSettings(settings, onChange) {
                return <ImportAssetAdvancedTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
