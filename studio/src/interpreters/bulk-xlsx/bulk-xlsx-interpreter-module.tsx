import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";
import { BulkXlsxInterpreterSettings } from "./bulk-xlsx-interpreter-settings";

export const BulkXlsxInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "bulkXlsx",
            label: "Bulk XLSX",
            renderSettings() {
                return <BulkXlsxInterpreterSettings />;
            },
        });
    },
};
