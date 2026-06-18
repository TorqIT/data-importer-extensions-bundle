import React from "react";
import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";
import { Alert } from "antd";

export const BulkSqlInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "bulkSql",
            label: "Bulk SQL",
            renderSettings() {
                return (
                    <Alert
                        message={"Bulk SQL interpreter uses the query configuration from the Bulk SQL loader."}
                        type="info"
                    />
                );
            },
        });
    },
};
