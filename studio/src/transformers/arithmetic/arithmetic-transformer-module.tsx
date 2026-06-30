import React from "react";
import { ArithmeticTransformerForm } from "./arithmetic-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const ArithmeticTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "arithmetic",
            label: "Arithmetic",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ArithmeticTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
