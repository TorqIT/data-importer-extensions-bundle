import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";
import { AdvancedXlsxInterpreterSettings } from "./advanced-xlsx-interpreter-settings";

export const AdvancedXlsxInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "advancedXlsx",
            label: "Advanced XLSX",
            renderSettings() {
                return <AdvancedXlsxInterpreterSettings />;
            },
        });
    },
};
