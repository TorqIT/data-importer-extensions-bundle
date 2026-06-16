import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getLoaderRegistry } from "../../common/consts/registries";
import { BulkSqlLoaderSettings } from "./bulk-sql-loader-settings";

export const BulkSqlLoaderModule: AbstractModule = {
    onInit() {
        getLoaderRegistry(container).registerDynamicType({
            id: "bulkSql",
            label: "Bulk SQL",
            renderSettings() {
                return <BulkSqlLoaderSettings />;
            },
        });
    },
};
