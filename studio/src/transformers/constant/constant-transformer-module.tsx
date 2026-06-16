import React from "react";
import { ConstantTransformerForm } from "./constant-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const ConstantTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "constant",
            label: "Constant",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <ConstantTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
