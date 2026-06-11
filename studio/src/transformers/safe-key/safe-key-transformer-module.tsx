import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const SafeKeyTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "safeKey",
            label: "Safe Key",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
