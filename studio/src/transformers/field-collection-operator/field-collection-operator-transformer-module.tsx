import React from "react";
import { FieldCollectionOperatorTransformerForm } from "./field-collection-operator-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const FieldCollectionOperatorTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "fieldCollectionOperator",
            label: "Field Collection Operator",
            group: "dataTypes",
            renderSettings(settings, onChange) {
                return <FieldCollectionOperatorTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
