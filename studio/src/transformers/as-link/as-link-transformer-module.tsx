import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const AsLinkTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "asLink",
            label: "As Link",
            group: "dataTypes",
            renderSettings() {
                return null;
            },
        });
    },
};
