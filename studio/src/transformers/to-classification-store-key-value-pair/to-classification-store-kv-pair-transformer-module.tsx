import React from "react";
import { ToClassificationStoreKvPairTransformerForm } from "./to-classification-store-kv-pair-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const ToClassificationStoreKvPairTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "toClassificationStoreKeyValuePair",
            label: "To Classification Store Key-Value Pair",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <ToClassificationStoreKvPairTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
