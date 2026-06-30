import { AbstractModule, container } from "@pimcore/studio-ui-bundle";
import { getTransformerRegistry } from "../../common/consts/registries";

export const SafeKeyTransformerModule: AbstractModule = {
    onInit() {
        getTransformerRegistry(container).registerDynamicType({
            id: "safeKey",
            label: "Safe Key",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
