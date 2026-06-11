import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const AsLinkTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "asLink",
            label: "As Link",
            group: "dataTypes",
            renderSettings() {
                return null;
            },
        });
    },
};
