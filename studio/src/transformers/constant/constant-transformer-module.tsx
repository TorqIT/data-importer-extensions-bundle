import React from "react";
import { ConstantTransformerForm } from "./constant-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const ConstantTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "constant",
            label: "Constant",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ConstantTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
