import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";
import { XmlSchemaBasedPreviewInterpreterSettings } from "./xml-schema-based-preview-interpreter-settings";

export const XmlSchemaBasedPreviewInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "XMLSchemaBasedPreview",
            label: "XML Schema Based Preview",
            renderSettings() {
                return <XmlSchemaBasedPreviewInterpreterSettings />;
            },
        });
    },
};
