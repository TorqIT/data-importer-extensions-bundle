import React from "react";
import { QuantityValueRangeArrayTransformerForm } from "./quantity-value-range-array-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const QuantityValueRangeArrayTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "quantityValueRangeArray",
            label: "Quantity Value Range Array",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <QuantityValueRangeArrayTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
