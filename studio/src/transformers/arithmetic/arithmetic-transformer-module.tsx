import React from "react";
import { ArithmeticTransformerForm } from "./arithmetic-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const ArithmeticTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "arithmetic",
            label: "Arithmetic",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ArithmeticTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
