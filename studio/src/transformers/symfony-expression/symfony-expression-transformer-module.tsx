import React from "react";
import { SymfonyExpressionTransformerForm } from "./symfony-expression-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const SymfonyExpressionTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "symfonyExpression",
            label: "Symfony Expression",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <SymfonyExpressionTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
