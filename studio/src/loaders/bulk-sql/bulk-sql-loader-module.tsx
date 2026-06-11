import React from "react";
import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { loaderRegistry } from "../../common/consts/registries";
import { BulkSqlLoaderSettings } from "./bulk-sql-loader-settings";

export const BulkSqlLoaderModule: AbstractModule = {
    onInit() {
        loaderRegistry.registerDynamicType({
            id: "bulkSql",
            label: "Bulk SQL",
            renderSettings() {
                return <BulkSqlLoaderSettings />;
            },
        });
    },
};