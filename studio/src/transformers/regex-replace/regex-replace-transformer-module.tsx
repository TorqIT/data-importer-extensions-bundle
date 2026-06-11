import React from "react";
import { RegexReplaceTransformerForm } from "./regex-replace-transformer-form";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const RegexReplaceTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "regexReplace",
            label: "Regex Replace",
            group: "dataManipulation",
            renderSettings(settings, onChange) {
                return <RegexReplaceTransformerForm onChange={onChange} settings={settings} />;
            },
        });
    },
};
