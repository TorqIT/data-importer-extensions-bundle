import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";
import { BulkCsvInterpreterSettings } from "./bulk-csv-interpreter-settings";

export const BulkCsvInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "bulkCsv",
            label: "Bulk CSV",
            renderSettings() {
                return <BulkCsvInterpreterSettings />;
            },
        });
    },
};
