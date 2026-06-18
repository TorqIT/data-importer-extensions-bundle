import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getInterpreterRegistry } from "../../common/consts/registries";

export const BulkSqlInterpreterModule: AbstractModule = {
    onInit() {
        getInterpreterRegistry(container).registerDynamicType({
            id: "bulkSql",
            label: "Bulk SQL",
            renderSettings() {
                return null;
            },
        });
    },
};
