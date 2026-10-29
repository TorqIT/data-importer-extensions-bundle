import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { LoadOrCreateByPathTransformerForm } from "./load-or-create-by-path-transformer-form";
import { getTransformerRegistry } from "../../common/consts/registries";

export const LoadOrCreateByPathTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "loadOrCreateByPath",
            label: "Load or Create by Path",
            group: "loadImport",
            renderSettings(settings, onChange) {
                return (
                  <LoadOrCreateByPathTransformerForm
                    onChange={ onChange }
                    settings={ settings }
                  />
                );
            },
        });
    },
};
