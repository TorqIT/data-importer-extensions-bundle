import React from "react";
import { LoadOrCreateDataObjectTransformerForm } from "./load-or-create-data-object-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

const LOAD_DATA_OBJECT_TYPE_ID = "loadDataObject";

export const LoadOrCreateDataObjectTransformerModule: AbstractModule = {
    onInit() {
        const registry = getTransformerRegistry(container);
        const coreLoadDataObject = registry.getDynamicType(LOAD_DATA_OBJECT_TYPE_ID);

        registry.overrideDynamicType({
            id: LOAD_DATA_OBJECT_TYPE_ID,
            label: "Load or Create Data Object",
            group: "loadImport",
            renderSettings(settings, onChange) {
                return (
                  <>
                    { coreLoadDataObject.renderSettings(settings, onChange) }
                    <LoadOrCreateDataObjectTransformerForm
                      onChange={ onChange }
                      settings={ settings }
                    />
                  </>
                );
            },
        });
    },
};
