import React from "react";
import { QuantityValueArrayTransformerForm } from "./quantity-value-array-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const QuantityValueArrayTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).overrideDynamicType({
            id: "quantityValueArray",
            label: "Quantity Value Array",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <QuantityValueArrayTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
