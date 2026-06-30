import React from "react";
import { RegexReplaceTransformerForm } from "./regex-replace-transformer-form";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const RegexReplaceTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "regexReplace",
            label: "Regex Replace",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <RegexReplaceTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
