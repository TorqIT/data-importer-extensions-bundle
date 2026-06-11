import { AbstractModule } from "@pimcore/studio-ui-bundle";
import { transformerRegistry } from "../../common/consts/registries";

export const EachAsArrayTransformerModule: AbstractModule = {
    onInit() {
        transformerRegistry.registerDynamicType({
            id: "eachAsArray",
            label: "Each As Array",
            group: "dataManipulation",
            renderSettings() {
                return null;
            },
        });
    },
};
