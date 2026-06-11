import React from "react";
import { ArrayValTransformerForm } from "./array-val-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const ArrayValTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "arrayVal",
            label: "Array Value",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ArrayValTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
