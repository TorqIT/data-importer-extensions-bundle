import React from "react";
import { QuantityValueRangeArrayTransformerForm } from "./quantity-value-range-array-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const QuantityValueRangeArrayTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "quantityValueRangeArray",
            label: "Quantity Value Range Array",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <QuantityValueRangeArrayTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
