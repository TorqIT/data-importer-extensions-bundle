import React from "react";
import { QuantityValueArrayTransformerForm } from "./quantity-value-array-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const QuantityValueArrayTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "quantityValueArray",
            label: "Quantity Value Array",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <QuantityValueArrayTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
