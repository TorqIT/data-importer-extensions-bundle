import React from "react";
import { ArrayValTransformerForm } from "./array-val-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const ArrayValTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "arrayVal",
            label: "Array Value",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ArrayValTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
