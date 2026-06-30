import React from "react";
import { SymfonyExpressionTransformerForm } from "./symfony-expression-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const SymfonyExpressionTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "symfonyExpression",
            label: "Symfony Expression",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <SymfonyExpressionTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
